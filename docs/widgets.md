# Widgets

## Built-in widgets

All built-in widgets are auto-registered. No setup needed.

| Alias | Class | Description |
|---|---|---|
| `stats-card` | `StatsCardWidget` | KPI stat block with icon, value, and trend |
| `chart-widget` | `ChartWidget` | Chart.js chart (bar/line/doughnut) |
| `recent-table` | `RecentTableWidget` | Table of recent records |

---

## Using widgets in views

All registered widgets are available via Livewire's `@livewire()` directive:

```blade
@livewire('widgets.stats-card', ['title' => 'Total Users', 'value' => '1,234'])
@livewire('widgets.chart-widget', ['type' => 'bar'])
@livewire('widgets.recent-table', ['title' => 'Recent Orders'])
```

Check if a widget is registered before rendering:

```php
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;

@if (WidgetRegistry::has('my-widget'))
    <livewire:widgets.my-widget />
@endif
```

---

## Creating a custom widget

### 1. Generate the class

```bash
php artisan panel:make-widget SalesChart
```

This creates `app/Livewire/Widgets/SalesChart.php` and its view.

### 2. Implement the widget

```php
namespace App\Livewire\Widgets;

use AlpDevelop\LivewirePanel\Widgets\AbstractWidget;
use Illuminate\Contracts\View\View;

final class SalesChart extends AbstractWidget
{
    public string $title = 'Ventas mensuales';

    public function data(): array
    {
        return [
            'labels' => ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            'values' => [1200, 1900, 1500, 2100, 1800],
        ];
    }

    public function render(): View
    {
        return view('livewire.widgets.sales-chart', [
            'data' => $this->data(),
        ]);
    }
}
```

### 3. Register the widget

Widgets can be registered from a Plugin or directly from `AppServiceProvider`:

```php
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use Livewire\Livewire;

// In AppServiceProvider::boot()
Livewire::component('widgets.sales-chart', SalesChart::class);

$registry = $this->app->make(WidgetRegistry::class);
$registry->register('sales-chart', SalesChart::class);
```

Or from a Plugin's `registerWidgets()` (see [plugins.md](plugins.md)).

---

## AbstractWidget options

`AbstractWidget` extends Livewire's `Component`. Available properties:

| Property | Type | Default | Description |
|---|---|---|---|
| `$title` | string | `''` | Widget title |
| `$pollSeconds` | int | `0` | Auto-refresh interval in seconds. `0` = disabled |

```php
final class MyWidget extends AbstractWidget
{
    public string $title = 'Active Users';
    public int $pollSeconds = 30;
}
```

---

## Displaying widgets on the dashboard

The dashboard page automatically renders all registered widgets from the `WidgetRegistry`. You can also manually add widget rendering to any Livewire page view:

```blade
<livewire:widgets.sales-chart />
```
