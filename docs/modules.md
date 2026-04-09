# Modules

## Built-in modules

All modules are optional. Register only the ones you need.

| Module | Class | Routes |
|---|---|---|
| Auth | `AlpDevelop\LivewirePanel\Modules\Auth\AuthModule` | `GET /{prefix}/login`, `GET /{prefix}/register`, `POST /{prefix}/logout` |
| Dashboard | `AlpDevelop\LivewirePanel\Modules\Dashboard\DashboardModule` | `GET /{prefix}` |
| Users | `AlpDevelop\LivewirePanel\Modules\Users\UsersModule` | `GET /{prefix}/users` |

The Auth module is always registered automatically by the package. The rest must be registered manually.

---

## Registering modules

In your `AppServiceProvider::register()`. **Module registration is optional** — if no modules are registered, the panel still works (login/logout) but has no internal pages.

```php
use AlpDevelop\LivewirePanel\Modules\Dashboard\DashboardModule;
use AlpDevelop\LivewirePanel\Modules\Users\UsersModule;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;

public function register(): void
{
    $registry = $this->app->make(ModuleRegistry::class);

    $registry->register('admin',    DashboardModule::class);
    $registry->register('admin',    UsersModule::class);
    $registry->register('operator', DashboardModule::class);
}
```

Each call to `register()` ties a module class to a specific panel ID.

---

## Creating a custom module

### 1. Generate the class

```bash
php artisan panel:make-module Inventario
```

This creates `app/Livewire/Modules/Inventario/InventarioModule.php` and a sample Livewire page component.

### 2. Implement the module

```php
namespace App\Livewire\Modules\Inventario;

use AlpDevelop\LivewirePanel\Modules\AbstractModule;
use AlpDevelop\LivewirePanel\Navigation\NavigationItem;
use Illuminate\Support\Facades\Route;

final class InventarioModule extends AbstractModule
{
    public function id(): string
    {
        return 'inventario';
    }

    public function routes(): void
    {
        $panelId = $this->panelId();
        $prefix  = $this->prefix();

        Route::middleware(['web', 'auth'])
            ->prefix($prefix)
            ->name("panel.{$panelId}.")
            ->group(function () {
                Route::get('/inventario', InventarioPage::class)->name('inventario.index');
            });
    }

    public function navigationItems(): array
    {
        return [
            new NavigationItem(
                label: 'Inventario',
                route: "panel.{$this->panelId()}.inventario.index",
                icon:  'archive-box',
                order: 30,
            ),
        ];
    }
}
```

### 3. Register it

```php
$registry->register('admin', InventarioModule::class);
```

---

## AbstractModule methods

| Method | Description |
|---|---|
| `id(): string` | Unique module identifier |
| `routes(): void` | Register Laravel routes |
| `publicRoutes(): void` | Register public (unauthenticated) routes |
| `navigationItems(): array` | Return `NavigationItem[]` to appear in sidebar |
| `userMenuItems(): array` | Return `NavigationItem[]` to appear in user dropdown menu |
| `permissions(): array` | Optional: list of permission strings this module uses |
| `panelId(): string` | Returns the panel ID from config |
| `prefix(): string` | Returns the URL prefix from config |
| `guard(): string` | Returns the auth guard from config |

---

## NavigationItem

```php
new NavigationItem(
    label: 'My Page',
    route: 'panel.admin.mypage',
    icon:  'cog-6-tooth',
    permission: 'mypage.view',
    roles: ['admin', 'editor'],
    description: 'Configuration page',
    keywords: 'config settings options',
);
```

| Property | Type | Required | Description |
|---|---|---|---|
| `label` | string | yes | Text displayed in the sidebar |
| `route` | string | yes | Laravel route name |
| `icon` | string | no | Heroicons outline icon name |
| `permission` | string | no | Required permission to show the item |
| `roles` | string\|array | no | Required role(s) to show the item |
| `badge` | Closure\|null | no | Callable returning a badge value |
| `description` | string | no | Description for search |
| `keywords` | string | no | Extra keywords for search |

Both `permission` and `roles` are optional. If both are set, the user must have the permission AND at least one of the roles for the item to be visible. The gate driver configured in the panel (`'spatie'`, `'laravel'`, or `null`) determines how these are evaluated. See [Navigation](navigation.md) for full details.
