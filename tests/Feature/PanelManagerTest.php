<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Facades\Panel;
use AlpDevelop\LivewirePanel\PanelManager;
use AlpDevelop\LivewirePanel\PanelPortalBuilder;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use AlpDevelop\LivewirePanel\Search\SearchRegistryInterface;
use AlpDevelop\LivewirePanel\Notifications\NotificationRegistryInterface;

final class PanelManagerTest extends PanelTestCase
{
    public function test_panel_manager_resolves_from_container(): void
    {
        $manager = $this->app->make(PanelManager::class);

        $this->assertInstanceOf(PanelManager::class, $manager);
    }

    public function test_panel_alias_resolves(): void
    {
        $manager = $this->app->make('panel');

        $this->assertInstanceOf(PanelManager::class, $manager);
    }

    public function test_facade_kernel_returns_kernel(): void
    {
        $kernel = Panel::kernel();

        $this->assertInstanceOf(\AlpDevelop\LivewirePanel\PanelKernel::class, $kernel);
    }

    public function test_facade_themes_returns_theme_registry(): void
    {
        $this->assertInstanceOf(ThemeRegistry::class, Panel::themes());
    }

    public function test_facade_modules_returns_module_registry(): void
    {
        $this->assertInstanceOf(ModuleRegistry::class, Panel::modules());
    }

    public function test_facade_plugins_returns_plugin_registry(): void
    {
        $this->assertInstanceOf(PluginRegistry::class, Panel::plugins());
    }

    public function test_facade_widgets_returns_widget_registry(): void
    {
        $this->assertInstanceOf(WidgetRegistry::class, Panel::widgets());
    }

    public function test_facade_search_returns_search_registry(): void
    {
        $this->assertInstanceOf(SearchRegistryInterface::class, Panel::search());
    }

    public function test_facade_notifications_returns_notification_registry(): void
    {
        $this->assertInstanceOf(NotificationRegistryInterface::class, Panel::notifications());
    }

    public function test_for_returns_portal_builder(): void
    {
        $builder = Panel::for('admin');

        $this->assertInstanceOf(PanelPortalBuilder::class, $builder);
    }

    public function test_clear_caches_runs_without_error(): void
    {
        Panel::clearCaches();
        $this->assertTrue(true);
    }
}
