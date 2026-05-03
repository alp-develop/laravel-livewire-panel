<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\Config\PanelStyleConfig;
use AlpDevelop\LivewirePanel\Modules\AbstractModule;
use AlpDevelop\LivewirePanel\Modules\Auth\AuthModule;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use AlpDevelop\LivewirePanel\Search\NavigationSearchProvider;
use AlpDevelop\LivewirePanel\Search\SearchRegistry;
use AlpDevelop\LivewirePanel\Themes\Bootstrap4Theme;
use AlpDevelop\LivewirePanel\Themes\Bootstrap5Theme;
use AlpDevelop\LivewirePanel\Themes\TailwindTheme;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Route;

final class PanelKernel
{
    private bool $booted = false;

    public function __construct(
        private readonly PanelConfig        $config,
        private readonly PanelStyleConfig   $styleConfig,
        private readonly ThemeRegistry      $themeRegistry,
        private readonly ModuleRegistry     $moduleRegistry,
        private readonly PluginRegistry     $pluginRegistry,
        private readonly NavigationRegistry $navigationRegistry,
        private readonly SearchRegistry     $searchRegistry,
        private readonly WidgetRegistry     $widgetRegistry,
    ) {}

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->pluginRegistry->boot();
        $this->bootThemes();
        $this->bootModules();
        $this->bootPublicPages();
        $this->bootSearchProviders();
        $this->bootPluginNavigation();
        $this->bootPluginWidgets();
        $this->pluginRegistry->afterBoot();

        $this->booted = true;
    }

    private function bootThemes(): void
    {
        $this->themeRegistry->register('bootstrap5', Bootstrap5Theme::class);
        $this->themeRegistry->register('bootstrap4', Bootstrap4Theme::class);
        $this->themeRegistry->register('tailwind',   TailwindTheme::class);
    }

    private function bootModules(): void
    {
        foreach ($this->config->all() as $panelConfig) {
            (new AuthModule($panelConfig))->routes();

            $navigationMode = $panelConfig['mode'] ?? 'config';

            foreach ($this->moduleRegistry->forPanel($panelConfig['id'] ?? '') as $moduleClass) {
                $module = new $moduleClass($panelConfig);
                assert($module instanceof AbstractModule);
                $module->routes();

                if ($navigationMode === 'modules') {
                    foreach ($module->navigationItems() as $item) {
                        $this->navigationRegistry->add($panelConfig['id'] ?? '', $item);
                    }
                }
            }

            if ($navigationMode === 'config') {
                $this->navigationRegistry->loadFromConfig(
                    $panelConfig['id'] ?? '',
                    $panelConfig['sidebar_menu'] ?? []
                );
            }
        }
    }

    private function bootPluginNavigation(): void
    {
        foreach ($this->pluginRegistry->allInstances() as $plugin) {
            foreach ($plugin->registerNavigation() as $panelId => $navItems) {
                foreach ($navItems as $item) {
                    $this->navigationRegistry->add($panelId, $item);
                }
            }
        }
    }

    private function bootPublicPages(): void
    {
        $bootedModules = [];

        foreach ($this->config->all() as $panelConfig) {
            foreach ($this->moduleRegistry->forPanel($panelConfig['id'] ?? '') as $moduleClass) {
                if (isset($bootedModules[$moduleClass])) {
                    continue;
                }

                $module = new $moduleClass($panelConfig);
                assert($module instanceof AbstractModule);
                $module->publicRoutes();
                $bootedModules[$moduleClass] = true;
            }
        }

        $pages = $this->config->raw()['public_pages'] ?? [];

        if ($pages === []) {
            return;
        }

        Route::middleware('web')->group(function () use ($pages): void {
            foreach ($pages as $page) {
                $middleware = $page['middleware'] ?? [];
                $route     = $page['route'] ?? null;
                $component = $page['component'] ?? null;
                $name      = $page['name'] ?? null;

                if ($route === null || $component === null) {
                    continue;
                }

                $r = Route::middleware($middleware)->get($route, $component);

                if ($name !== null) {
                    $r->name($name);
                }
            }
        });
    }

    private function bootSearchProviders(): void
    {
        foreach ($this->config->all() as $panelConfig) {
            $panelId = $panelConfig['id'] ?? '';
            $this->searchRegistry->register($panelId, new NavigationSearchProvider());
        }
    }

    private function bootPluginWidgets(): void
    {
        foreach ($this->pluginRegistry->allInstances() as $plugin) {
            foreach ($plugin->registerWidgets() as $alias => $class) {
                $this->widgetRegistry->register($alias, $class);
            }
        }
    }

    public function config(): PanelConfig
    {
        return $this->config;
    }

    public function styleConfig(): PanelStyleConfig
    {
        return $this->styleConfig;
    }

    public function themes(): ThemeRegistry
    {
        return $this->themeRegistry;
    }

    public function modules(): ModuleRegistry
    {
        return $this->moduleRegistry;
    }

    public function plugins(): PluginRegistry
    {
        return $this->pluginRegistry;
    }

    public function widgets(): WidgetRegistry
    {
        return $this->widgetRegistry;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }
}
