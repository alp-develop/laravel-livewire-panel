# Panel Facade

The `Panel` facade provides a single, ergonomic access point to the package's core registries and utilities. It is automatically aliased when using Laravel's package discovery.

---

## Basic usage

```php
use Panel;

// Get the current panel ID resolved from the request
$panelId = Panel::currentId();

// Generate a route for a panel
$url = Panel::route('home');
$url = Panel::route('users.index');

// Generate a route for a specific panel
$url = Panel::for('admin')->route('home');
$url = Panel::for('admin')->home();
```

---

## Accessing registries

```php
use Panel;

// Theme registry
$theme = Panel::themes()->resolve('bootstrap5');

// Module registry
$modules = Panel::modules()->forPanel('admin');

// Plugin registry
Panel::plugins()->boot();

// Widget registry
Panel::widgets()->register('my-widget', MyWidget::class);

// Search registry
Panel::search()->register('admin', new MySearchProvider());
$results = Panel::search()->search('dashboard', 'admin');

// Notification registry
Panel::notifications()->register('admin', new MyNotificationProvider());
```

---

## Accessing the kernel

```php
use Panel;

$kernel = Panel::kernel();

// Check if the kernel is booted
$kernel->isBooted();

// Access panel config
$config = $kernel->config()->get('admin');

// Access style config
$styleConfig = $kernel->styleConfig()->get('default');
```

---

## Clearing caches

```php
use Panel;

// Clears SearchRegistry memoization cache and PanelRenderer static cache
Panel::clearCaches();
```

This is useful after programmatically modifying navigation, search providers, or style configuration at runtime.

---

## Helper functions

The package also provides global helper functions:

| Function | Description |
|----------|-------------|
| `panel_route(string $panelId, string $routeName, array $parameters = [])` | Generate a named route for a panel |
| `to_panel(string $panelId)` | Return a `PanelPortalBuilder` for a panel |
| `panel_component(string $key)` | Resolve the class for a built-in panel component |

```php
// Using helpers
$url = panel_route('admin', 'home');
$url = to_panel('admin')->home();
$url = to_panel('admin')->route('users.index');
```

---

## Method reference

| Method | Return | Description |
|--------|--------|-------------|
| `Panel::currentId()` | `string` | Resolves the current panel ID from the request |
| `Panel::for(string $panelId)` | `PanelPortalBuilder` | Returns a builder scoped to a panel |
| `Panel::route(string $routeName, array $parameters = [])` | `string` | Generates a route for the current panel |
| `Panel::kernel()` | `PanelKernel` | Returns the booted PanelKernel instance |
| `Panel::themes()` | `ThemeRegistry` | Returns the theme registry |
| `Panel::modules()` | `ModuleRegistry` | Returns the module registry |
| `Panel::plugins()` | `PluginRegistry` | Returns the plugin registry |
| `Panel::widgets()` | `WidgetRegistry` | Returns the widget registry |
| `Panel::search()` | `SearchRegistryInterface` | Returns the search registry |
| `Panel::notifications()` | `NotificationRegistryInterface` | Returns the notification registry |
| `Panel::clearCaches()` | `void` | Clears all memoized caches |
