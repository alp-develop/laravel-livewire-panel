<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

use Illuminate\Contracts\View\View;

interface WidgetInterface
{
    public function canView(): bool;

    public function render(): View;
}
