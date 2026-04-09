# Plugins

## Creating a plugin

Plugins add cross-cutting functionality to panels: routes, navigation items, widgets, and lifecycle hooks. Unlike modules (which are tied to one panel), plugins can contribute to multiple panels at once.

### 1. Generate the class

```bash
php artisan panel:make-plugin Reportes
```

This creates `app/Plugins/ReportesPlugin.php`.

### 2. Implement the plugin

```php
namespace App\Plugins;

use AlpDevelop\LivewirePanel\Navigation\NavigationItem;
use AlpDevelop\LivewirePanel\Plugins\AbstractPlugin;
use App\Livewire\ReportesPage;
use App\Livewire\Widgets\VentasDiarias;
use Illuminate\Support\Facades\Route;

final class ReportesPlugin extends AbstractPlugin
{
    public function id(): string
    {
        return 'reportes';
    }

    public function beforeBoot(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix('admin')
            ->name('panel.admin.')
            ->group(function () {
                Route::get('/reportes', ReportesPage::class)->name('reportes');
            });
    }

    public function registerNavigation(): array
    {
        return [
            'admin' => [
                new NavigationItem(
                    label: 'Reportes',
                    route: 'panel.admin.reportes',
                    icon:  'document-chart-bar',
                    order: 60,
                ),
            ],
        ];
    }

    public function registerWidgets(): array
    {
        return [
            'ventas-diarias' => VentasDiarias::class,
        ];
    }

    public function afterBoot(): void
    {
        \Livewire\Livewire::component('widgets.ventas-diarias', VentasDiarias::class);
    }
}
```

## Registering a plugin

In `AppServiceProvider::register()`:

```php
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use App\Plugins\ReportesPlugin;

$pluginRegistry = $this->app->make(PluginRegistry::class);
$pluginRegistry->register(ReportesPlugin::class);
```

---

## AbstractPlugin lifecycle

| Method | Called when | Use for |
|---|---|---|
| `beforeBoot(): void` | Panel kernel boots | Registering routes |
| `registerNavigation(): array` | After boot | Adding nav items per panel |
| `registerWidgets(): array` | After boot | Declaring widget aliases and classes |
| `afterBoot(): void` | After all modules and plugins boot | Registering Livewire components, side effects |

---

## registerNavigation() format

Return an associative array of `panelId => NavigationItem[]`:

```php
public function registerNavigation(): array
{
    return [
        'admin' => [
            new NavigationItem(label: 'Reports', route: 'panel.admin.reports', icon: 'chart-bar'),
        ],
        'operator' => [
            new NavigationItem(label: 'My Reports', route: 'panel.operator.reports', icon: 'chart-bar'),
        ],
    ];
}
```

---

## PluginInterface

If you prefer not to extend `AbstractPlugin`, implement `PluginInterface` directly:

```php
interface PluginInterface
{
    public function id(): string;
    public function beforeBoot(): void;
    public function registerNavigation(): array;
    public function registerWidgets(): array;
    public function afterBoot(): void;
}
```
