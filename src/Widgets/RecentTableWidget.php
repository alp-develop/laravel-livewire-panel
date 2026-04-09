<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;

#[Lazy]
final class RecentTableWidget extends AbstractWidget
{
    public array  $headers   = [];
    public array  $rows      = [];
    public int    $limit     = 5;
    public string $emptyText = 'Sin datos disponibles';

    public function render(): View
    {
        return view('panel::widgets.recent-table');
    }
}
