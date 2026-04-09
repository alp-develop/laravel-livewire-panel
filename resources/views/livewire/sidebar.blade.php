<aside class="panel-sidebar">
    <div class="panel-sidebar-inner">
        <div class="panel-sidebar-brand">
            @if (!empty($sidebarLogo))
                <img src="{{ $sidebarLogo }}" alt="{{ $headerText }}" class="{{ $logoClass }}" style="height:{{ $logoHeight }};width:{{ $logoWidth }};flex-shrink:0;object-fit:contain" />
            @else
                <x-panel::icon name="layer-group" style="flex-shrink:0;width:{{ $logoWidth !== 'auto' ? $logoWidth : 'auto' }};height:calc({{ $logoHeight }} * 0.5)" />
            @endif
            <span class="sidebar-brand-name">{{ $headerText }}</span>
        </div>

        @if (!empty($panelConfig['back_to']))
            @php $backPanelId = $panelConfig['back_to']; @endphp
            @if (Route::has("panel.{$backPanelId}.home"))
                <div style="padding:.5rem .75rem;border-bottom:1px solid rgba(255,255,255,0.08)">
                    <a href="{{ route("panel.{$backPanelId}.home") }}"
                        style="display:flex;align-items:center;gap:.6rem;color:#64748b;font-size:.8rem;text-decoration:none;padding:.45rem .5rem;border-radius:6px;transition:background .15s"
                        onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.color='#94a3b8'"
                        onmouseout="this.style.background='transparent';this.style.color='#64748b'">
                        <x-panel::icon name="arrow-left" size="15" style="flex-shrink:0" />
                        <span class="sidebar-label">{{ __('panel::messages.back_to', ['name' => ucfirst($backPanelId)]) }}</span>
                    </a>
                </div>
            @endif
        @endif

        <nav class="panel-sidebar-nav">
            @foreach ($navItems as $item)
                @if ($item instanceof \AlpDevelop\LivewirePanel\Navigation\NavigationGroup)
                    @php
                        $canSeeGroup = (empty($item->permission) || $gate->allows($item->permission))
                            && (empty($item->roles) || $gate->hasRole($item->roles));
                    @endphp
                    @if ($canSeeGroup)
                        <div class="panel-nav-group" x-data="{ open: false }">
                            <button
                                type="button"
                                class="panel-nav-item panel-nav-group-toggle"
                                @click="open = !open"
                            >
                                <x-panel::icon :name="$item->icon ?: 'folder'" size="18" />
                                <span class="sidebar-label">{{ __($item->label) }}</span>
                                <x-panel::icon name="chevron-down" size="14" class="panel-nav-chevron" ::class="open ? 'panel-nav-chevron-open' : ''" />
                            </button>
                            <div class="panel-nav-group-children" x-show="open" x-collapse>
                                @foreach ($item->children as $child)
                                    @php
                                        $canSeeChild = (empty($child->permission) || $gate->allows($child->permission))
                                            && (empty($child->roles) || $gate->hasRole($child->roles));
                                    @endphp
                                    @if ($canSeeChild)
                                        @php
                                            $childPath = rtrim(parse_url(route($child->route), PHP_URL_PATH), '/');
                                            $isChildActive = $activePath === $childPath;
                                        @endphp
                                        <a
                                            href="{{ route($child->route) }}"
                                            wire:navigate
                                            class="panel-nav-item panel-nav-child {{ $isChildActive ? 'active' : '' }}"
                                        >
                                            <x-panel::icon :name="$child->icon ?: 'layer-group'" size="16" />
                                            <span class="sidebar-label">{{ $child->label }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    @php
                        $canSeeItem = (empty($item->permission) || $gate->allows($item->permission))
                            && (empty($item->roles) || $gate->hasRole($item->roles));
                    @endphp
                    @if ($canSeeItem)
                        @php
                            $itemPath = rtrim(parse_url(route($item->route), PHP_URL_PATH), '/');
                            $isActive = $activePath === $itemPath;
                        @endphp
                        <a
                            href="{{ route($item->route) }}"
                            wire:navigate
                            class="panel-nav-item {{ $isActive ? 'active' : '' }}"
                        >
                            <x-panel::icon :name="$item->icon ?: 'layer-group'" size="18" />
                            <span class="sidebar-label">{{ __($item->label) }}</span>
                        </a>
                    @endif
                @endif
            @endforeach
        </nav>

        @if ($showUserMenu && $user)
            <div class="panel-sidebar-user"
                x-data="{
                    open: false,
                    popStyle: '',
                    toggle() {
                        this.open = !this.open;
                        if (this.open) {
                            this.$nextTick(() => {
                                let rect = this.$refs.trigger.getBoundingClientRect();
                                let isMobile = window.innerWidth <= 768;
                                if (isMobile) {
                                    let left = rect.left + 8;
                                    let bottom = window.innerHeight - rect.top + 8;
                                    this.popStyle = 'bottom:' + bottom + 'px;left:' + left + 'px;max-width:calc(100vw - ' + (left + 16) + 'px)';
                                } else {
                                    let left = rect.right + 8;
                                    let bottom = window.innerHeight - rect.bottom;
                                    if (left + 260 > window.innerWidth) { left = rect.left - 260 - 8; }
                                    this.popStyle = 'bottom:' + bottom + 'px;left:' + left + 'px';
                                }
                            });
                        }
                    }
                }"
                @keydown.escape.window="open = false"
            >
                <button type="button" class="panel-sidebar-user-trigger" x-ref="trigger" @click="toggle()">
                    @if ($showAvatar && $avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="panel-sidebar-avatar" style="object-fit:cover" />
                    @elseif ($showAvatar)
                        <span class="panel-sidebar-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                    <span class="sidebar-label panel-sidebar-user-info">
                        <span class="panel-sidebar-user-name">{{ $user->name }}</span>
                    </span>
                    <x-panel::icon name="ellipsis-vertical" size="16" class="sidebar-label" style="margin-left:auto;opacity:.5;flex-shrink:0" />
                </button>
                <template x-teleport="body">
                <div class="panel-sidebar-user-popover" x-show="open" x-transition :style="popStyle" @click.outside="open = false" x-cloak>
                    <div class="panel-sidebar-user-popover-header">
                        @if ($showAvatar && $avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="panel-sidebar-avatar panel-sidebar-avatar--lg" style="object-fit:cover" />
                        @elseif ($showAvatar)
                            <span class="panel-sidebar-avatar panel-sidebar-avatar--lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                        <div style="min-width:0">
                            <div class="panel-sidebar-user-popover-name">{{ $user->name }}</div>
                            <div class="panel-sidebar-user-popover-email">{{ $user->email }}</div>
                        </div>
                    </div>
                    @if (!empty($userMenu))
                        @foreach ($userMenu as $menuItem)
                            @if (($menuItem['type'] ?? '') === 'divider')
                                <div class="panel-sidebar-user-popover-divider"></div>
                            @else
                                @if (Route::has($menuItem['route'] ?? ''))
                                    <a href="{{ route($menuItem['route']) }}" wire:navigate class="panel-sidebar-user-popover-item" @click="open = false">
                                        <x-panel::icon :name="$menuItem['icon'] ?? 'layer-group'" size="16" />
                                        {{ __($menuItem['label'] ?? '') }}
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @if (Route::has($profileRoute))
                            <a href="{{ route($profileRoute) }}" wire:navigate class="panel-sidebar-user-popover-item" @click="open = false">
                                <x-panel::icon name="user" size="16" />
                                {{ __('panel::messages.profile') }}
                            </a>
                        @endif
                    @endif
                    <div class="panel-sidebar-user-popover-divider"></div>
                    @if (Route::has($logoutRoute))
                        <form method="POST" action="{{ route($logoutRoute) }}" style="margin:0">
                            @csrf
                            <button type="submit" class="panel-sidebar-user-popover-item panel-sidebar-user-popover-item--danger">
                                <x-panel::icon name="right-from-bracket" size="16" />
                                {{ __('panel::messages.sign_out') }}
                            </button>
                        </form>
                    @endif
                </div>
                </template>
            </div>
        @endif

    </div>
</aside>
