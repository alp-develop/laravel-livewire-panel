<div class="panel-navbar-wrap">
<header class="panel-navbar">
    <button class="panel-navbar-icon-btn" onclick="togglePanelSidebar()" title="{{ __('panel::messages.toggle_sidebar') }}">
        <x-panel::icon name="bars" size="20" />
    </button>

    @if ($showPageTitle)
    <div class="panel-navbar-title">{{ $pageTitle }}</div>
    @else
    <div class="panel-navbar-title" style="flex:1"></div>
    @endif

    @foreach ($navbarComponentsLeft as $componentLeft)
        @livewire($componentLeft, key('navbar-left-' . $loop->index))
    @endforeach

    <div class="panel-navbar-actions">
        @if ($showSearch)
        <button class="panel-navbar-icon-btn" title="{{ __('panel::messages.search') }} (Ctrl+K)" x-on:click="$dispatch('open-panel-search')">
            <x-panel::icon name="magnifying-glass" size="20" />
        </button>
        @endif

        @if ($darkModeEnabled)
        <button class="panel-navbar-icon-btn" onclick="togglePanelDarkMode()" title="{{ __('panel::messages.toggle_dark_mode') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" class="panel-dark-icon-moon"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 8.002-4.248Z"/></svg>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" class="panel-dark-icon-sun"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
        </button>
        @endif

        @if ($localeEnabled && count($availableLocales) > 1)
        <div class="panel-dropdown" x-data="{ open: false }" x-on:click.outside="open = false" @keydown.escape.window="open = false">
            <button type="button" class="panel-locale-trigger" x-on:click="open = !open" title="{{ __('panel::messages.language') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                <span>{{ strtoupper($currentLocale) }}</span>
                <x-panel::icon name="chevron-down" size="12" />
            </button>
            <div class="panel-sidebar-user-popover panel-navbar-popover" x-show="open" x-transition x-cloak style="min-width:140px">
                @foreach ($availableLocales as $code => $label)
                    <button wire:click="switchLocale('{{ $code }}')" @click="open = false" style="display:flex;align-items:center;justify-content:space-between;gap:8px;width:100%;padding:8px 14px;background:none;border:none;cursor:pointer;text-align:left;font-size:.85rem;color:var(--panel-text-primary,#334155);{{ $code === $currentLocale ? 'font-weight:600;background:var(--panel-content-bg,#f8fafc)' : '' }}" onmouseover="this.style.background='var(--panel-content-bg,#f1f5f9)'" onmouseout="this.style.background='{{ $code === $currentLocale ? 'var(--panel-content-bg,#f8fafc)' : 'transparent' }}'">
                        <span>{{ $label }}</span>
                        @if ($code === $currentLocale)
                            <x-panel::icon name="check" size="14" style="color:var(--panel-primary)" />
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
        @endif

        @if ($showNotifications)
        @livewire('panel-notifications', ['polling' => $notificationPolling, 'pollingInterval' => $notificationPollingInterval])
        @endif

        @foreach ($navbarComponentsRight as $componentRight)
            @livewire($componentRight, key('navbar-right-' . $loop->index))
        @endforeach

        @if ($user && $showUserMenu)
            <div class="panel-dropdown" x-data="{ open: false }" x-on:click.outside="open = false" @keydown.escape.window="open = false">
                <button class="panel-user-trigger" x-on:click="open = !open">
                    @if ($showAvatar && $avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="panel-navbar-avatar" style="object-fit:cover" />
                    @elseif ($showAvatar)
                        <span class="panel-navbar-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                    <span class="panel-user-name">{{ $user->name }}</span>
                    <x-panel::icon name="chevron-down" size="14" class="panel-user-chevron" />
                </button>
                <div class="panel-sidebar-user-popover panel-navbar-popover" x-show="open" x-transition x-cloak>
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
            </div>
        @endif
    </div>
</header>
</div>
