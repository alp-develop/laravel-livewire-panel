<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Icon extends Component
{
    public function __construct(
        public string $name,
        public string $size  = '20',
        public string $class = '',
    ) {}

    public function render(): View
    {
        return view('panel::components.icon');
    }
}
