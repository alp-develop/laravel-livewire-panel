<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Livewire;

use AlpDevelop\LivewirePanel\Auth\PanelGate;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Modules\NavigationItem;
use AlpDevelop\LivewirePanel\Navigation\NavigationGroup;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelRenderer;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class AbstractNavbar extends Component
{
    #[Locked]
    public string $panelId = '';

    #[Locked]
    public string $title   = '';

    public function mount(string $title = ''): void
    {
        $context       = app(PanelContext::class);
        $this->panelId = $context->resolved()
            ? $context->get()
            : app(PanelResolver::class)->resolveFromRequest(request());
        $this->title   = $title;
    }

    public function render(): View
    {
        $panelConfig = app(PanelResolver::class)->resolveById($this->panelId);
        $guard       = (string) ($panelConfig['guard'] ?? 'web');
        $user        = auth()->guard($guard)->user();

        $layoutConfig = PanelRenderer::layoutConfig();

        $avatarUrl = null;
        $avatarResolver = $layoutConfig['avatar_resolver'] ?? null;
        if ($avatarResolver && is_callable($avatarResolver) && $user) {
            $avatarUrl = $avatarResolver($user);
        }

        $localeConfig    = $panelConfig['locale'] ?? [];
        $localeEnabled   = (bool) ($localeConfig['enabled'] ?? false);
        $availableLocales = $localeConfig['available'] ?? [];
        $currentLocale   = app()->getLocale();

        $navbarComponents = $panelConfig['navbar_components'] ?? [];

        return view($this->view(), [
            'user'              => $user,
            'logoutRoute'       => "panel.{$this->panelId}.auth.logout",
            'profileRoute'      => "panel.{$this->panelId}.profile.index",
            'userMenu'          => $this->resolveUserMenu($panelConfig),
            'showSearch'        => $layoutConfig['show_search'],
            'showNotifications' => $layoutConfig['show_notifications'],
            'notificationPolling' => $layoutConfig['notification_polling'],
            'notificationPollingInterval' => $layoutConfig['notification_polling_interval'],
            'showUserMenu'      => $layoutConfig['show_user_menu'],
            'darkModeEnabled'   => $layoutConfig['dark_mode'],
            'showPageTitle'     => $layoutConfig['show_page_title'] ?? true,
            'showAvatar'        => $layoutConfig['navbar_show_avatar'] ?? true,
            'sidebarCollapsible' => $layoutConfig['sidebar_collapsible'] ?? true,
            'avatarUrl'         => $avatarUrl,
            'userPopoverComponent' => $layoutConfig['user_popover_header_component'] ?? null,
            'localeEnabled'     => $localeEnabled,
            'availableLocales'  => $availableLocales,
            'currentLocale'     => $currentLocale,
            'pageTitle'         => $this->resolvePageTitle(),
            'navbarComponentsLeft'  => $this->filterNavbarComponents($navbarComponents['left'] ?? []),
            'navbarComponentsRight' => $this->filterNavbarComponents($navbarComponents['right'] ?? []),
        ]);
    }

    public function switchLocale(string $locale): void
    {
        $panelConfig = app(PanelResolver::class)->resolveById($this->panelId);
        $available   = $panelConfig['locale']['available'] ?? [];

        if (!array_key_exists($locale, $available)) {
            return;
        }

        session()->put('panel_locale', $locale);
        app()->setLocale($locale);

        $this->js('location.reload()');
    }

    protected function view(): string
    {
        return 'panel::livewire.navbar';
    }

    private function resolvePageTitle(): string
    {
        $currentRoute = request()->route()?->getName() ?? '';

        if ($currentRoute === '') {
            return __($this->title ?: 'Panel');
        }

        $map = app(NavigationRegistry::class)->buildRouteMap($this->panelId);

        return isset($map[$currentRoute]) ? __($map[$currentRoute]) : __($this->title ?: 'Panel');
    }

    /** @param array<string, mixed> $panelConfig
     *  @return list<array<string, mixed>> */
    private function resolveUserMenu(array $panelConfig): array
    {
        $mode = $panelConfig['mode'] ?? 'config';

        if ($mode === 'modules') {
            $items = [];
            $moduleRegistry = app(ModuleRegistry::class);
            $panelId = $panelConfig['id'] ?? '';

            foreach ($moduleRegistry->forPanel($panelId) as $moduleClass) {
                $module = new $moduleClass($panelConfig);
                assert($module instanceof \AlpDevelop\LivewirePanel\Modules\AbstractModule);
                foreach ($module->userMenuItems() as $moduleItem) {
                    $items[] = $moduleItem;
                }
            }
        } else {
            $items = $panelConfig['user_menu'] ?? [];
        }

        if (empty($items)) {
            return [];
        }

        $gate     = app(PanelGate::class);
        $filtered = [];

        foreach ($items as $item) {
            if (($item['type'] ?? '') === 'divider') {
                $filtered[] = $item;
                continue;
            }

            if (isset($item['visible']) && !$this->resolveVisibility($item['visible'])) {
                continue;
            }

            $hasPermission = empty($item['permission']) || $gate->allows($item['permission']);
            $hasRole       = empty($item['roles']) || $gate->hasRole($item['roles']);

            if ($hasPermission && $hasRole) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    /** @param array<int, string|array<string, mixed>> $components
     * @return array<int, string> */
    private function filterNavbarComponents(array $components): array
    {
        if ($components === []) {
            return [];
        }

        $gate     = app(PanelGate::class);
        $filtered = [];

        foreach ($components as $component) {
            if (is_string($component)) {
                $filtered[] = $component;
                continue;
            }

            $name = $component['component'] ?? null;
            if ($name === null) {
                continue;
            }

            if (isset($component['visible']) && !$this->resolveVisibility($component['visible'])) {
                continue;
            }

            $hasPermission = empty($component['permission']) || $gate->allows($component['permission']);
            $hasRole       = empty($component['roles']) || $gate->hasRole($component['roles']);

            if ($hasPermission && $hasRole) {
                $filtered[] = $name;
            }
        }

        return $filtered;
    }

    private function resolveVisibility(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_callable($value)) {
            return (bool) $value();
        }

        if (is_string($value) && class_exists($value)) {
            return (bool) app($value)();
        }

        return true;
    }
}
