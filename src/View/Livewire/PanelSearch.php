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
        foreach ($groups as $group) {
            $totalResults += count($group['items']);
        }

        return view('panel::livewire.search', [
            'groups'       => $groups,
            'totalResults' => $totalResults,
        ]);
    }
}
