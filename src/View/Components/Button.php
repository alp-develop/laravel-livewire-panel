<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use AlpDevelop\LivewirePanel\View\Concerns\ResolvesActiveTheme;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Button extends Component
{
    use ResolvesActiveTheme;

    public function __construct(
        public string $variant = 'primary',
        public string $type    = 'button',
        public string $size    = 'md',
    ) {}

    public function render(): View
    {
        return view('panel::components.button', ['theme' => $this->resolveTheme()]);
    }
}
