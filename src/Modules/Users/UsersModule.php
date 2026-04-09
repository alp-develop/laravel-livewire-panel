<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Users;

use AlpDevelop\LivewirePanel\Compat\LivewireCompat;
use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
use AlpDevelop\LivewirePanel\Modules\AbstractModule;
use AlpDevelop\LivewirePanel\Modules\NavigationItem;
use AlpDevelop\LivewirePanel\Modules\Users\Http\Livewire\UsersPage;
use Illuminate\Support\Facades\Route;

final class UsersModule extends AbstractModule
{
    public function id(): string
    {
        return 'users';
    }

    public function routes(): void
    {
        $panelId = $this->panelId();
        $prefix  = $this->prefix();

        Route::middleware(['web', PanelAuthMiddleware::class])
            ->prefix($prefix . '/users')
            ->name("panel.{$panelId}.users.")
            ->group(function () {
                LivewireCompat::pageRoute('/', UsersPage::class)->name('index');
            });
    }

    public function navigationItems(): array
    {
        return [
            new NavigationItem(
                label: 'panel::sidebar.users',
                route: 'panel.' . $this->panelId() . '.users.index',
                icon: 'users',
                description: 'Manage users, roles and permissions',
                keywords: 'users accounts members list create edit delete roles permissions',
            ),
        ];
    }

    public function permissions(): array
    {
        return ['users.view', 'users.create', 'users.edit', 'users.delete'];
    }
}
