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
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class AbstractSidebar extends Component
{
    #[Locked]
    public string $panelId    = '';

    #[Locked]
    public string $activePath = '';

    public function mount(): void
    {
        $context          = app(PanelContext::class);
        $this->panelId    = $context->resolved()
            ? $context->get()
            : app(PanelResolver::class)->resolveFromRequest(request());
        $this->activePath = rtrim(request()->getPathInfo(), '/');
    }

    public function render(): View
    {
        $navRegistry  = app(NavigationRegistry::class);
        $panelConfig  = app(PanelResolver::class)->resolveById($this->panelId);
        $layoutConfig = PanelRenderer::layoutConfig();
        $guard        = (string) ($panelConfig['guard'] ?? 'web');
        $user         = auth()->guard($guard)->user();

        $avatarUrl = null;
        $avatarResolver = $layoutConfig['avatar_resolver'] ?? null;
        if ($avatarResolver && is_callable($avatarResolver) && $user) {
            $avatarUrl = $avatarResolver($user);
        }

        $gate         = app(PanelGate::class);

        return view($this->view(), [
            'navItems'        => $this->buildNavItems($navRegistry->forPanel($this->panelId), $gate),
            'backTo'          => $this->buildBackTo($panelConfig),
            'panelConfig'     => $panelConfig,
            'darkModeEnabled' => $layoutConfig['dark_mode'] ?? false,
            'sidebarLogo'     => $layoutConfig['sidebar_logo'] ?? null,
            'logoHeight'      => $layoutConfig['sidebar_logo_height'] ?? '40px',
            'logoWidth'       => $layoutConfig['sidebar_logo_width'] ?? 'auto',
            'logoClass'       => $layoutConfig['sidebar_logo_class'] ?? '',
            'headerText'      => $layoutConfig['sidebar_header_text'] ?? 'Panel Admin',
            'showUserMenu'    => $layoutConfig['sidebar_show_user_menu'] ?? false,
            'showAvatar'      => $layoutConfig['sidebar_show_avatar'] ?? true,
            'avatarUrl'       => $avatarUrl,
            'user'            => $user,
            'logoutRoute'     => "panel.{$this->panelId}.auth.logout",
            'profileRoute'    => "panel.{$this->panelId}.profile.index",
            'userMenu'        => $this->resolveUserMenu($panelConfig),
        ]);
    }

    protected function view(): string
    {
        return 'panel::livewire.sidebar';
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

    /** @param array<int, NavigationItem|NavigationGroup> $items
     *  @return list<array<string, mixed>> */
    private function buildNavItems(array $items, PanelGate $gate): array
    {
        $result = [];

        foreach ($items as $item) {
            if ($item instanceof NavigationGroup) {
                $canSee = (empty($item->permission) || $gate->allows($item->permission))
                    && (empty($item->roles) || $gate->hasRole($item->roles));

                if (!$canSee) {
                    continue;
                }

                $children       = [];
                $hasActiveChild = false;

                foreach ($item->children as $child) {
                    $canSeeChild = (empty($child->permission) || $gate->allows($child->permission))
                        && (empty($child->roles) || $gate->hasRole($child->roles));

                    if (!$canSeeChild) {
                        continue;
                    }

                    try {
                        $childPath = rtrim(parse_url(route($child->route), PHP_URL_PATH) ?: '', '/');
                    } catch (\Throwable) {
                        continue;
                    }

                    $isActive = $this->activePath === $childPath;

                    if ($isActive) {
                        $hasActiveChild = true;
                    }

                    $children[] = [
                        'type'   => 'item',
                        'label'  => $child->label,
                        'route'  => $child->route,
                        'icon'   => $child->icon,
                        'active' => $isActive,
                    ];
                }

                $result[] = [
                    'type'     => 'group',
                    'label'    => $item->label,
                    'icon'     => $item->icon,
                    'open'     => $hasActiveChild,
                    'children' => $children,
                ];

                continue;
            }

            $canSee = (empty($item->permission) || $gate->allows($item->permission))
                && (empty($item->roles) || $gate->hasRole($item->roles));

            if (!$canSee) {
                continue;
            }

            try {
                $path = rtrim(parse_url(route($item->route), PHP_URL_PATH) ?: '', '/');
            } catch (\Throwable) {
                continue;
            }

            $result[] = [
                'type'   => 'item',
                'label'  => $item->label,
                'route'  => $item->route,
                'icon'   => $item->icon,
                'active' => $this->activePath === $path,
            ];
        }

        return $result;
    }

    /** @param array<string, mixed> $panelConfig
     *  @return array{id: string, route: string}|null */
    private function buildBackTo(array $panelConfig): ?array
    {
        $backId = (string) ($panelConfig['back_to'] ?? '');

        if ($backId === '') {
            return null;
        }

        $routeName = "panel.{$backId}.home";

        if (!Route::has($routeName)) {
            return null;
        }

        return ['id' => $backId, 'route' => $routeName];
    }
}
