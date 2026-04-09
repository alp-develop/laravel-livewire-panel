<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class Portal extends Component
{
    public function __construct(
        public readonly string $panel,
        public readonly string $route = 'home',
        public readonly array  $params = [],
    ) {}

    public function url(): string
    {
        return panel_route($this->panel, $this->route, $this->params);
    }

    public function render(): View
    {
        return view('panel::components.portal');
    }
}
