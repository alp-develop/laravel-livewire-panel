<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use AlpDevelop\LivewirePanel\View\Concerns\ResolvesActiveTheme;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Card extends Component
{
    use ResolvesActiveTheme;

    public function __construct(
        public string $title  = '',
    ) {}

    public function render(): View
    {
        return view('panel::components.card', ['theme' => $this->resolveTheme()]);
    }
}
