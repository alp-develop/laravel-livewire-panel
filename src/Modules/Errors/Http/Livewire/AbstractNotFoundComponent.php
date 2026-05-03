<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Errors\Http\Livewire;

use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class AbstractNotFoundComponent extends Component
{
    #[Locked]
    public string $panelId = '';

    public function mount(): void
    {
        $this->panelId = app(PanelResolver::class)->resolveFromRequest(request());
    }

    abstract public function render(): View;
}
