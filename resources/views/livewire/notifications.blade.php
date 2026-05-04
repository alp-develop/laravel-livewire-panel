<div @if ($polling) wire:poll.{{ $pollingInterval }}s @endif x-data="{ open: false }" x-on:click.outside="open = false" class="panel-dropdown">
    <button class="panel-navbar-icon-btn" x-on:click="open = !open" title="{{ __('panel::messages.notifications') }}" style="position:relative">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
        @if ($count > 0)
            <span class="panel-notification-badge">{{ $count > 99 ? '99+' : $count }}</span>
        @endif
    </button>

    <div class="panel-dropdown-menu panel-dropdown-menu--right panel-notification-dropdown" x-show="open" x-cloak x-transition>
        <div class="panel-notification-header">
            <span>{{ __('panel::messages.notifications') }}</span>
            @if ($count > 0)
                <button type="button" wire:click.prevent="markAllAsRead" class="panel-notification-mark-all" x-on:click.prevent.stop>
                    {{ __('panel::messages.mark_all_as_read') }}
                </button>
            @endif
        </div>

        @if (count($items) > 0)
            <div class="panel-notification-list">
                @foreach ($items as $item)
                    <div wire:key="notif-{{ $item['id'] }}" class="panel-notification-item {{ empty($item['read']) ? 'panel-notification-item--unread' : '' }}">
                        <div style="display:flex;align-items:flex-start;padding:0.7rem 1rem;gap:0.65rem">
                            @if (!empty($item['route']))
                                <a href="{{ $item['route'] }}" wire:navigate x-on:click="open = false" style="display:flex;align-items:flex-start;gap:0.65rem;flex:1;min-width:0;text-decoration:none;color:inherit">
                            @else
                                <div style="display:flex;align-items:flex-start;gap:0.65rem;flex:1;min-width:0">
                            @endif
                                <div class="panel-notification-icon-wrap" style="{{ $item['safeColor'] !== '' ? 'background:' . $item['safeColor'] . '20;color:' . $item['safeColor'] : '' }}">
                                    <x-panel::icon :name="$item['icon'] ?? 'bell'" size="16" />
                                </div>
                                <div class="panel-notification-body">
                                    <div class="panel-notification-title">{{ $item['title'] }}</div>
                                    @if (!empty($item['body']))
                                        <div class="panel-notification-text">{{ $item['body'] }}</div>
                                    @endif
                                    @if (!empty($item['time']))
                                        <div class="panel-notification-time">{{ $item['time'] }}</div>
                                    @endif
                                </div>
                            @if (!empty($item['route']))
                                </a>
                            @else
                                </div>
                            @endif
                            @if (empty($item['read']))
                                <button type="button" wire:click.prevent="markAsRead({{ \Illuminate\Support\Js::from($item['id']) }})" class="panel-notification-dismiss" title="{{ __('panel::messages.mark_as_read') }}" style="align-self:center;flex-shrink:0">
                                    <x-panel::icon name="check" size="14" />
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="panel-dropdown-empty">{{ __('panel::messages.no_notifications') }}</div>
        @endif
    </div>
</div>
