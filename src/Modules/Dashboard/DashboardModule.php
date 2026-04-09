<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Dashboard;

use AlpDevelop\LivewirePanel\Compat\LivewireCompat;
use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
use AlpDevelop\LivewirePanel\Modules\AbstractModule;
use AlpDevelop\LivewirePanel\Modules\Dashboard\Http\Livewire\DashboardPage;
use AlpDevelop\LivewirePanel\Modules\NavigationItem;
use Illuminate\Support\Facades\Route;

final class DashboardModule extends AbstractModule
{
    public function id(): string
    {
        return 'dashboard';
    }

    public function routes(): void
    {
        $panelId = $this->panelId();
        $prefix  = $this->prefix();

        Route::middleware(['web', PanelAuthMiddleware::class])
            ->prefix($prefix)
            ->name("panel.{$panelId}.")
            ->group(function () {
                LivewireCompat::pageRoute('/', DashboardPage::class)->name('home');
            });
    }

    public function navigationItems(): array
    {
        return [
            new NavigationItem(
                label: 'panel::sidebar.dashboard',
                route: 'panel.' . $this->panelId() . '.home',
                icon: 'house',
                description: 'Overview, statistics and recent activity',
                keywords: 'home overview stats charts widgets analytics summary',
            ),
        ];
    }
}
