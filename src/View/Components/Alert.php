<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use AlpDevelop\LivewirePanel\View\Concerns\ResolvesActiveTheme;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Alert extends Component
{
    use ResolvesActiveTheme;

    public function __construct(
        public string $variant     = 'info',
        public bool   $dismissible = false,
    ) {}

    public function render(): View
    {
        return view('panel::components.alert', ['theme' => $this->resolveTheme()]);
    }
}
