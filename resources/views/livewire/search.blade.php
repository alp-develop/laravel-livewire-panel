<div
    x-data="{ open: false, selectedIndex: 0 }"
    x-on:open-panel-search.window="open = true; selectedIndex = 0; $nextTick(() => $refs.searchInput.focus())"
    x-on:keydown.escape.window="open = false"
    x-on:keydown.meta.k.window.prevent="open = !open; if(open) { selectedIndex = 0; $nextTick(() => $refs.searchInput.focus()) }"
    x-on:keydown.ctrl.k.window.prevent="open = !open; if(open) { selectedIndex = 0; $nextTick(() => $refs.searchInput.focus()) }"
    x-show="open"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="panel-search-overlay"
    style="display:none"
>
    <div class="panel-search-backdrop" x-on:click="open = false"></div>

    <div
        class="panel-search-modal"
        x-on:keydown.arrow-down.prevent="selectedIndex = Math.min(selectedIndex + 1, {{ max($totalResults - 1, 0) }})"
        x-on:keydown.arrow-up.prevent="selectedIndex = Math.max(selectedIndex - 1, 0)"
        x-on:keydown.enter.prevent="
            let link = $el.querySelector('[data-search-index=\'' + selectedIndex + '\'] a');
            if(link) link.click();
        "
        x-trap.noscroll="open"
    >
        <div class="panel-search-header">
            <x-panel::icon name="magnifying-glass" size="18" style="color:var(--panel-text-muted,#94a3b8);flex-shrink:0" />
            <input
                x-ref="searchInput"
                wire:model.live.debounce.200ms="query"
                type="text"
                class="panel-search-input"
                placeholder="{{ __('panel::messages.search_placeholder') }}"
                autocomplete="off"
                x-on:input="selectedIndex = 0"
            />
            <kbd class="panel-search-kbd panel-search-kbd-desktop">ESC</kbd>
            <button type="button" class="panel-search-close-mobile" x-on:click="open = false">
                <x-panel::icon name="xmark" size="20" />
            </button>
        </div>

        <div class="panel-search-results" x-ref="searchResults">
            @if ($totalResults > 0)
                @foreach ($groups as $group)
                    <div class="panel-search-category">
                        <x-panel::icon :name="$group['icon']" size="14" />
                        <span>{{ $group['category'] }}</span>
                    </div>
                    @foreach ($group['items'] as $item)
                        <div
                            class="panel-search-item"
                            data-search-index="{{ $item['index'] }}"
                            :class="selectedIndex === {{ $item['index'] }} ? 'panel-search-item--active' : ''"
                            x-on:mouseenter="selectedIndex = {{ $item['index'] }}"
                        >
                            <a href="{{ $item['url'] }}" wire:navigate x-on:click="open = false" class="panel-search-link">
                                <x-panel::icon :name="$item['icon'] ?? 'layer-group'" size="18" class="panel-search-item-icon" />
                                <div class="panel-search-item-text">
                                    <span class="panel-search-item-label">{!! $item['labelHighlighted'] !!}</span>
                                    @if ($item['descHighlighted'] !== '')
                                        <span class="panel-search-item-desc">{!! $item['descHighlighted'] !!}</span>
                                    @endif
                                </div>
                                <x-panel::icon name="arrow-right" size="14" class="panel-search-item-arrow" />
                            </a>
                        </div>
                    @endforeach
                @endforeach
            @else
                <div class="panel-search-empty">
                    @if (trim($query) !== '')
                        {{ __('panel::messages.no_results_for', ['query' => $query]) }}
                    @else
                        {{ __('panel::messages.type_to_search') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
