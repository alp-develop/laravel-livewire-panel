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

    public function mount(): void
    {
        $context       = app(PanelContext::class);
        $this->panelId = $context->resolved()
            ? $context->get()
            : app(PanelResolver::class)->resolveFromRequest(request());
    }

    public function render(): View
    {
        $registry = app(SearchRegistry::class);
        $groups   = $registry->search($this->query, $this->panelId);

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
