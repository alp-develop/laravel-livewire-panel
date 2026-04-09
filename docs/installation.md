# Installation

## Install the package

```bash
composer require alp-develop/laravel-livewire-panel
```

## Run the installer

```bash
php artisan panel:install
```

The command publishes the config file, style files and runs an interactive menu to configure your first panel.

### Flags

| Flag | Description |
|---|---|
| `--defaults` | Skip interactive prompts and generate config with all default values |
| `--force` | Overwrite existing config and style files |

---

## Interactive menu

When you run `panel:install` without `--defaults`, the installer asks the following questions in order:

### URL prefix

```
URL prefix (default: admin)
```

The route prefix for the panel. Determines the base URL: `http://yourapp.test/{prefix}/login`.

Default: `admin`

### Navigation mode

```
Navigation mode
> Config  — Sidebar and Navbar defined manually in configuration array
  Modules — Sidebar and Navbar built automatically from registered modules
```

Controls how sidebar and navbar items are built:

| Mode | Description |
|---|---|
| `config` | Sidebar and Navbar items are defined in the `sidebar_menu` array of the panel config. You control the exact order, labels, icons and grouping. Routes must exist (registered by modules or in `routes/web.php`). |
| `modules` | Sidebar and Navbar items come from registered modules via their `navigationItems()` method. The `sidebar_menu` array stays empty and each module defines its own items. |

Default: `config`

Module routes are always registered regardless of the mode. The mode only controls where sidebar items come from.

See [Navigation](navigation.md) for full details on both modes, categories, permissions and roles.

### CSS Theme

```
CSS Theme
> Bootstrap 5
  Bootstrap 4
  Tailwind CSS
```

The CSS framework used for the panel layout and components.

| Value | Framework |
|---|---|
| `bootstrap5` | Bootstrap 5 |
| `bootstrap4` | Bootstrap 4 |
| `tailwind` | Tailwind CSS |

Default: `bootstrap5`

See [Themes](themes.md) for creating custom themes.

### Gate driver

```
Gate driver (permission system)
> None   — No permission checks
  Spatie — Uses spatie/laravel-permission
  Laravel — Uses Laravel's built-in Gate
```

Determines how permission checks are handled for navigation items and module access.

| Value | Description |
|---|---|
| `null` | No permission checks. All authenticated users see all items. |
| `spatie` | Uses `spatie/laravel-permission`. Navigation items with `permission` are checked against the user's Spatie permissions. |
| `laravel` | Uses Laravel's built-in Gate. Navigation items with `permission` are checked via `Gate::allows()`. |

Default: `null` (none)

### Registration

```
Enable user registration? (yes/no)
```

Whether the panel shows a registration page at `/{prefix}/register`.

Default: `no`

### CDN libraries

```
CDN libraries to include
> [x] Chart.js       — Charts and graphs
  [ ] SweetAlert2    — Alert dialogs
  [ ] Select2        — Enhanced selects
  [ ] Flatpickr      — Date picker
```

External libraries loaded via CDN. Each adds CSS and/or JS to pages where they are needed (globally or per route).

| Library | Description |
|---|---|
| `chartjs` | Chart.js for graphs and charts |
| `sweetalert2` | SweetAlert2 for alert dialogs |
| `select2` | Select2 for enhanced select inputs |
| `flatpickr` | Flatpickr for date pickers |

Default: `chartjs`

See [Configuration > CDN](configuration.md) for customizing CDN entries and restricting them to specific routes.

### Publish views

```
Publish package views for customization? (yes/no)
```

Copies the package Blade views to `resources/views/vendor/panel/` so you can modify them.

Default: `no`

See [Components](components.md) for overriding specific components per panel.

---

## Navigation modes

### Config mode (default)

Sidebar items are defined directly in the `sidebar_menu` array. Routes must exist (registered by modules or in `routes/web.php`):

```php
'mode' => 'config',
'sidebar_menu' => [
    ['label' => 'Dashboard', 'route' => 'panel.admin.dashboard.index', 'icon' => 'home'],
    ['label' => 'Users', 'route' => 'panel.admin.users.index', 'icon' => 'users'],
],
```

### Modules mode

Sidebar items are built automatically from registered modules. Each module defines its own `navigationItems()`:

```php
'mode' => 'modules',
'sidebar_menu' => [],
```

Register modules in `AppServiceProvider::register()`:

```php
use AlpDevelop\LivewirePanel\Modules\Dashboard\DashboardModule;
use AlpDevelop\LivewirePanel\Modules\Users\UsersModule;
use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;

$registry = $this->app->make(ModuleRegistry::class);
$registry->register('admin', DashboardModule::class);
$registry->register('admin', UsersModule::class);
```

Module routes are always registered regardless of the mode. The mode only controls where sidebar items come from.

See [Navigation](navigation.md) and [Modules](modules.md) for full details.

---

## Generated config

After the interactive menu, the installer writes `config/laravel-livewire-panel.php` with your choices. With all defaults, it generates:

```php
return [

    'default' => 'admin',

    'panels' => [

        'admin' => [
            'id'                   => 'admin',
            'prefix'               => 'admin',
            'guard'                => 'web',
            'theme'                => 'bootstrap5',
            'customization'        => 'style_table',
            'middleware'           => ['web', 'auth'],
            'gate'                 => null,
            'registration_enabled' => false,
            'mode'                 => 'config',
            'sidebar_menu' => [
                ['label' => 'Dashboard', 'route' => 'panel.admin.dashboard.index', 'icon' => 'home'],
                [
                    'label' => 'Management',
                    'icon'  => 'cog-6-tooth',
                    'children' => [
                        ['label' => 'Users', 'route' => 'panel.admin.users.index', 'icon' => 'users'],
                    ],
                ],
            ],
            'user_menu' => [],
            'components' => [
                'login'           => null,
                'register'        => null,
                'forgot-password' => null,
                'sidebar'         => null,
                'navbar'          => null,
            ],
            'cdn' => [
                'chartjs' => [
                    'css'    => [],
                    'js'     => ['https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js'],
                    'routes' => [],
                ],
            ],
        ],

    ],

];
```

---

## Next steps

After installation, you need to create your first page. The `prefix` in the panel config determines the base URL — for example, `'prefix' => 'admin'` places all panel routes under `/admin/*`. After login, the user is redirected to `/{prefix}`. Route names must follow the pattern `panel.{panelId}.{name}` so the sidebar can resolve them.

### Create a page manually (config mode)

1. Create a Livewire component:

```php
namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard');
    }
}
```

2. Create the Blade view:

```blade
<div>
    <h1>Dashboard</h1>
</div>
```

3. Register the route in `routes/web.php` with the panel auth middleware. The `prefix` and `name` must match the panel config:

```php
use App\Livewire\Dashboard;
use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;

Route::middleware(['web', PanelAuthMiddleware::class])
    ->prefix('admin')
    ->name('panel.admin.')
    ->group(function () {
        Route::get('/', Dashboard::class)->name('home');
    });
```

4. Add the route to the `sidebar_menu` in `config/laravel-livewire-panel.php`:

```php
'sidebar_menu' => [
    ['label' => 'Dashboard', 'route' => 'panel.admin.home', 'icon' => 'home'],
],
```

5. Visit `http://yourapp.test/admin/login`. After login, you will be redirected to `/admin` where your page renders inside the panel layout.

For modules mode, see [Modules](modules.md).

---

For further configuration, see:

- [Configuration](configuration.md) — all panel config keys, multi-panel setup, CDN, guards
- [Navigation](navigation.md) — categories, subcategories, permissions, roles, user menu
- [Modules](modules.md) — built-in modules and creating custom ones
- [Widgets](widgets.md) — dashboard widgets
- [Customization](customization.md) — CSS variables, sidebar, navbar, dark mode
