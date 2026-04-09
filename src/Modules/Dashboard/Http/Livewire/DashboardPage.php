<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Dashboard\Http\Livewire;

use AlpDevelop\LivewirePanel\PanelContext;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.app', ['title' => 'Dashboard'])]
final class DashboardPage extends Component
{
    public function render(): View
    {
        $panelId     = app(PanelContext::class)->get();
        $panelConfig = config("laravel-livewire-panel.panels.{$panelId}", []);
        $stats       = $panelConfig['dashboard_stats'] ?? [];

        return view('panel::modules.dashboard.page', ['stats' => $stats]);
    }
}
