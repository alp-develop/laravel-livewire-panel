<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Livewire;

use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;
use AlpDevelop\LivewirePanel\Search\SearchRegistry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

final class PanelSearch extends Component
{
    #[Locked]
    public string $panelId = '';

    public string $query = '';

    private ?string $lastExecutedQuery = null;

    /** @var array<int, mixed> */
    private array $lastResults = [];

    public function mount(): void
    {
        $context       = app(PanelContext::class);
        $this->panelId = $context->resolved()
            ? $context->get()
            : app(PanelResolver::class)->resolveFromRequest(request());
    }

    public function updatedQuery(): void
    {
        $this->query = mb_substr($this->query, 0, 100);
    }

    public function render(): View
    {
        $groups = [];

        if (mb_strlen($this->query) !== 1) {
            if ($this->query !== $this->lastExecutedQuery) {
                $this->lastExecutedQuery = $this->query;
                $this->lastResults       = app(SearchRegistry::class)->search($this->query, $this->panelId);
            }

            $groups = $this->lastResults;
        }

        $totalResults = 0;
        $index        = 0;

        foreach ($groups as &$group) {
            foreach ($group['items'] as &$item) {
                $item['index']                = $index++;
                $item['labelHighlighted']     = $this->highlight($item['label'] ?? '', $this->query);
                $item['descHighlighted']      = isset($item['description']) && $item['description'] !== ''
                    ? $this->highlight($item['description'], $this->query)
                    : '';
                $totalResults++;
            }
        }
        unset($group, $item);

        return view('panel::livewire.search', [
            'groups'       => $groups,
            'totalResults' => $totalResults,
        ]);
    }

    private function highlight(string $text, string $query): string
    {
        if (trim($query) === '') {
            return e($text);
        }

        $escaped = preg_quote($query, '/');

        return preg_replace(
            '/(' . $escaped . ')/iu',
            '<mark class="panel-search-mark">$1</mark>',
            e($text)
        ) ?? e($text);
    }
}
