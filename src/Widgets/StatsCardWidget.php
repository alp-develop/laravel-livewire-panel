<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;

#[Lazy]
final class StatsCardWidget extends AbstractWidget
{
    public string $value   = '';
    public string $icon    = 'chart-bar';
    public string $trend   = '';
    public string $trendType = 'neutral';
    public string $color   = 'primary';

    public function render(): View
    {
        return view('panel::widgets.stats-card');
    }
}
