<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Livewire;

use AlpDevelop\LivewirePanel\Auth\PanelGate;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelRenderer;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
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

        return view($this->view(), [
            'navItems'        => $navRegistry->forPanel($this->panelId),
            'activePath'      => $this->activePath,
            'panelConfig'     => $panelConfig,
            'gate'            => app(PanelGate::class),
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

    private function resolveUserMenu(array $panelConfig): array
    {
        $mode = $panelConfig['mode'] ?? 'config';

        if ($mode === 'modules') {
            $items = [];
            $moduleRegistry = app(ModuleRegistry::class);
            $panelId = $panelConfig['id'] ?? '';

            foreach ($moduleRegistry->forPanel($panelId) as $moduleClass) {
                $module = new $moduleClass($panelConfig);
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
}
