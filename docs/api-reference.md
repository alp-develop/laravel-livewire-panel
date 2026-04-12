# API Reference

Public interfaces and main classes reference.

---

## Interfaces

### ThemeInterface

`AlpDevelop\LivewirePanel\Themes\ThemeInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `id()` | `string` | Unique theme identifier |
| `cssAssets()` | `array` | CSS stylesheet URLs |
| `jsAssets()` | `array` | JavaScript script URLs |
| `headHtml(array $styleConfig = [])` | `string` | Extra HTML for `<head>` |
| `cssVariables(array $styleConfig)` | `string` | Custom CSS variables for the theme |
| `componentClasses()` | `array` | CSS class map per component |

Built-in implementations: `Bootstrap5Theme`, `Bootstrap4Theme`, `TailwindTheme`.

---

### ModuleInterface

`AlpDevelop\LivewirePanel\Modules\ModuleInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `id()` | `string` | Unique module identifier |
| `navigationItems()` | `array` | Sidebar navigation items |
| `userMenuItems()` | `array` | User menu items |
| `routes()` | `void` | Register protected routes |
| `publicRoutes()` | `void` | Register public routes |
| `permissions()` | `array` | Permissions required by the module |

Built-in modules: `DashboardModule`, `UsersModule`, `AuthModule`.

---

### WidgetInterface

`AlpDevelop\LivewirePanel\Widgets\WidgetInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `canView()` | `bool` | Determines if the widget is visible |
| `render()` | `View` | Renders the widget view |

Built-in widgets: `StatsCardWidget`, `ChartWidget`, `RecentTableWidget`.

---

### PluginInterface

`AlpDevelop\LivewirePanel\Plugins\PluginInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `id()` | `string` | Unique plugin identifier |
| `beforeBoot()` | `void` | Hook executed before boot |
| `afterBoot()` | `void` | Hook executed after boot |
| `registerNavigation()` | `array` | Plugin navigation items |
| `registerWidgets()` | `array` | Plugin widget classes |

---

### GateDriverInterface

`AlpDevelop\LivewirePanel\Auth\GateDriverInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `check(string $permission, mixed $user = null)` | `bool` | Check a permission |
| `hasRole(string\|array $roles, mixed $user = null)` | `bool` | Check user roles |

Built-in drivers: `LaravelGateDriver`, `SpatieGateDriver`, `NullGateDriver`.

---

### SearchProviderInterface

`AlpDevelop\LivewirePanel\Search\SearchProviderInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `category()` | `string` | Search category name |
| `icon()` | `string` | Category icon |
| `search(string $query, string $panelId)` | `array` | Execute the search |

---

### NotificationProviderInterface

`AlpDevelop\LivewirePanel\Notifications\NotificationProviderInterface`

| Method | Return | Description |
|--------|--------|-------------|
| `count(string $panelId)` | `int` | Total unread notifications |
| `items(string $panelId, int $limit = 10)` | `array` | Notification list |
| `markAsRead(string $id, string $panelId)` | `void` | Mark a notification as read |
| `markAllAsRead(string $panelId)` | `void` | Mark all as read |

See [Notifications](notifications.md) for usage guide, item structure, and registration examples.

---

## Main Classes

### PanelKernel

`AlpDevelop\LivewirePanel\PanelKernel`

Central panel orchestrator. Boots themes, modules, plugins, navigation, widgets and search.

| Method | Description |
|--------|-------------|
| `boot()` | Boot all panel subsystems |

---

### PanelResolver

`AlpDevelop\LivewirePanel\PanelResolver`

Resolves the active panel configuration.

| Method | Return | Description |
|--------|--------|-------------|
| `resolveFromRequest(Request $request)` | `string` | Resolve panel ID from the request |
| `resolveById(string $id)` | `array` | Get panel config by ID |
| `hasPanel(string $id)` | `bool` | Check if a panel exists |

---

### PanelRenderer

`AlpDevelop\LivewirePanel\PanelRenderer`

Generates CSS/JS assets and panel variables. Implements internal memoization.

| Method | Return | Description |
|--------|--------|-------------|
| `cssVars(string $panelId)` | `string` | Panel CSS variables |
| `layoutConfig(string $panelId)` | `array` | Layout configuration |
| `cssAssets(string $panelId)` | `array` | CSS asset URLs |
| `jsAssets(string $panelId)` | `array` | JS asset URLs |
| `assetUrl(string $path)` | `string` | Package asset URL |

---

### AbstractWidget

`AlpDevelop\LivewirePanel\Widgets\AbstractWidget`

Base widget class. Extends `Livewire\Component`.

| Property | Type | Description |
|----------|------|-------------|
| `$title` | `string` | Widget title |
| `$pollSeconds` | `int` | Polling interval (0 = disabled) |

---

### AbstractTheme

`AlpDevelop\LivewirePanel\Themes\AbstractTheme`

Base theme class with shared logic.

| Method | Description |
|--------|-------------|
| `sanitizeCssValue(string $value)` | Sanitize CSS values against injection |
| `resolveThemeColors(array $styleConfig)` | Resolve theme colors (primary, secondary, etc.) |
| `resolveDarkColors(array $styleConfig)` | Resolve dark mode colors |
| `cssVariables(array $styleConfig)` | Base CSS variables (panel-*) |
| `darkCssVariables(array $styleConfig)` | Dark mode CSS variables |
| `classes(string $component, string $slot)` | Component CSS classes |

---

### AbstractModule

`AlpDevelop\LivewirePanel\Modules\AbstractModule`

Base module class with default implementations.

| Method | Default | Description |
|--------|---------|-------------|
| `navigationItems()` | `[]` | Navigation items |
| `userMenuItems()` | `[]` | User menu items |
| `routes()` | no-op | Register protected routes |
| `publicRoutes()` | no-op | Register public routes |
| `permissions()` | `[]` | Module permissions |

---

## Registries

| Class | Description |
|-------|-------------|
| `ThemeRegistry` | Available themes registry |
| `ModuleRegistry` | Modules per panel registry |
| `WidgetRegistry` | Available widgets registry |
| `PluginRegistry` | Plugins registry |
| `NavigationRegistry` | Navigation items registry |
| `SearchRegistry` | Search providers registry |

---

## Events

All events extend `AlpDevelop\LivewirePanel\Events\PanelEvent`.

| Event | Dispatched when |
|-------|----------------|
| `LoginAttempted` | A login is attempted |
| `UserLoggedIn` | Successful login |
| `UserLoggedOut` | User logs out |
| `UserRegistered` | User registration |
| `UserCreated` | User created from panel |
| `UserUpdated` | User updated |
| `UserDeleted` | User deleted |
| `PanelAccessDenied` | Panel access denied |

See [events.md](events.md) for full details.
