<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;

#[Lazy]
final class ChartWidget extends AbstractWidget
{
    public string $type     = 'line';
    public array  $labels   = [];
    public array  $datasets = [];
    public int    $height   = 260;

    public function render(): View
    {
        return view('panel::widgets.chart-widget');
    }
}
