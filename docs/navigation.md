# Navigation

## Navigation modes

Each panel supports two navigation modes to define sidebar items. Configure it with the `mode` key in the panel configuration (`config/laravel-livewire-panel.php`):

| Mode | Value | Description |
|------|-------|-------------|
| Config | `'config'` | Sidebar items are defined directly in the `sidebar_menu` array of the panel config. This is the default mode. |
| Modules | `'modules'` | Sidebar items are defined in each module via `navigationItems()`. |

**Module routes are always registered**, regardless of the navigation mode. The mode only controls where sidebar items are read from.

---

## Modules mode

When `mode` is `'modules'`, each registered module defines its own navigation items through the `navigationItems()` method:

```php
// config/laravel-livewire-panel.php
'admin' => [
    'id'              => 'admin',
    'prefix'          => 'admin',
    'mode' => 'modules',
    // ...
],
```

```php
// In your module
final class ReportsModule extends AbstractModule
{
    public function routes(): void
    {
        Route::middleware(['web', PanelAuthMiddleware::class])
            ->prefix($this->prefix() . '/reports')
            ->name("panel.{$this->panelId()}.reports.")
            ->group(function () {
                Route::get('/', ReportsPage::class)->name('index');
            });
    }

    public function navigationItems(): array
    {
        return [
            new NavigationItem(
                label: 'Reports',
                route: 'panel.' . $this->panelId() . '.reports.index',
                icon: 'chart-bar',
            ),
        ];
    }
}
```

---

## Config mode

Sidebar items are defined in the `sidebar_menu` array of the panel config. This allows controlling the sidebar structure without creating modules:

```php
// config/laravel-livewire-panel.php
'admin' => [
    'id'              => 'admin',
    'prefix'          => 'admin',
    'mode' => 'config',
    'sidebar_menu'    => [
        [
            'label' => 'Dashboard',
            'route' => 'panel.admin.dashboard.index',
            'icon'  => 'home',
        ],
        [
            'label' => 'Users',
            'route' => 'panel.admin.users.index',
            'icon'  => 'users',
        ],
    ],
    // ...
],
```

Each item requires at minimum `label` and `route`. The `icon` field is optional.

> **Tip:** Labels support translation keys. Use `'label' => 'panel::sidebar.dashboard'` to translate automatically. See [Localization](localization.md).

> **Note:** referenced routes must exist. You can define them in your own route files (`routes/web.php`) or keep using modules to register routes while controlling the sidebar from config.

---

## Categories and subcategories

In `config` mode, you can group items into categories using the `children` field. An entry with `children` renders as a collapsible group in the sidebar:

```php
'sidebar_menu' => [
    [
        'label' => 'Dashboard',
        'route' => 'panel.admin.dashboard.index',
        'icon'  => 'home',
    ],
    [
        'label'    => 'Management',
        'icon'     => 'cog-6-tooth',
        'children' => [
            [
                'label' => 'Users',
                'route' => 'panel.admin.users.index',
                'icon'  => 'users',
            ],
            [
                'label' => 'Roles',
                'route' => 'panel.admin.roles.index',
                'icon'  => 'shield-check',
            ],
        ],
    ],
    [
        'label'    => 'Content',
        'icon'     => 'document-text',
        'children' => [
            [
                'label' => 'Articles',
                'route' => 'panel.admin.articles.index',
                'icon'  => 'newspaper',
            ],
            [
                'label' => 'Categories',
                'route' => 'panel.admin.categories.index',
                'icon'  => 'tag',
            ],
        ],
    ],
],
```

Groups render with a collapsible toggle (Alpine.js `x-collapse`). You can mix standalone items and categories in any order.

---

## Permissions

In both `modules` and `config` modes, each item and group supports the `permission` field. If the authenticated user does not have the permission, the item is hidden from the sidebar.

### In config mode

```php
'sidebar_menu' => [
    [
        'label'      => 'Dashboard',
        'route'      => 'panel.admin.dashboard.index',
        'icon'       => 'home',
        // No permission: visible to everyone
    ],
    [
        'label'      => 'Users',
        'route'      => 'panel.admin.users.index',
        'icon'       => 'users',
        'permission' => 'users.view',
        // Only visible if the user has the 'users.view' permission
    ],
],
```

### In modules mode

```php
public function navigationItems(): array
{
    return [
        new NavigationItem(
            label: 'Users',
            route: 'panel.' . $this->panelId() . '.users.index',
            icon: 'users',
            permission: 'users.view',
        ),
    ];
}
```

### In categories

If a category has `permission`, the entire category (including its children) is hidden if the user lacks the permission. Children can also have their own individual `permission`:

```php
[
    'label'      => 'Management',
    'icon'       => 'cog-6-tooth',
    'permission' => 'manage-system',
    'children'   => [
        ['label' => 'Users', 'route' => '...', 'permission' => 'users.view'],
        ['label' => 'Roles', 'route' => '...', 'permission' => 'roles.view'],
    ],
],
```

---

## Roles

In addition to permissions, each item and group supports the `roles` field to filter by user role. Accepts a string (one role) or an array (any of the roles):

```php
'sidebar_menu' => [
    [
        'label' => 'Admin Panel',
        'route' => 'panel.admin.dashboard.index',
        'icon'  => 'home',
        'roles' => 'admin',
        // Only visible to users with the 'admin' role
    ],
    [
        'label' => 'Reports',
        'route' => 'panel.admin.reports.index',
        'icon'  => 'chart-bar',
        'roles' => ['admin', 'manager'],
        // Visible to users with the 'admin' OR 'manager' role
    ],
],
```

### In modules mode

```php
new NavigationItem(
    label: 'Reports',
    route: 'panel.' . $this->panelId() . '.reports.index',
    icon: 'chart-bar',
    roles: ['admin', 'manager'],
)
```

---

## Combining permissions and roles

You can use `permission` and `roles` together. **Both conditions must be met** for the item to be visible:

```php
[
    'label'      => 'Advanced Settings',
    'route'      => 'panel.admin.settings.advanced',
    'icon'       => 'wrench',
    'permission' => 'settings.advanced',
    'roles'      => 'super-admin',
    // The user must have the 'settings.advanced' permission AND the 'super-admin' role
],
```

| permission | roles | Result |
|------------|-------|--------|
| empty | empty | Visible to everyone |
| set | empty | Visible only if the user has the permission |
| empty | set | Visible only if the user has the role |
| set | set | Visible only if the user has both |

---

## Gate drivers

Permission and role verification is delegated to the package's gate system, configurable per panel with the `gate` key:

| Value | Driver | Permissions | Roles |
|-------|--------|-------------|-------|
| `'spatie'` | `SpatiGateDriver` | `$user->hasPermissionTo()` | `$user->hasRole()` / `$user->hasAnyRole()` |
| `'laravel'` | `LaravelGateDriver` | `Gate::allows()` | `$user->hasRole()` if method exists, otherwise allows all |
| `null` | `NullGateDriver` | Always allows | Always allows |

```php
// config/laravel-livewire-panel.php
'admin' => [
    'gate' => 'spatie', // uses spatie/laravel-permission
    // ...
],

'public' => [
    'gate' => null,     // no restrictions
    // ...
],
```

To use Spatie, install the package:

```bash
composer require spatie/laravel-permission
```

And make sure your User model uses the `HasRoles` trait:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

---

## Navbar components

You can inject custom Livewire components into the navbar without creating a custom navbar class. Use the `navbar_components` key in the panel configuration:

```php
// config/laravel-livewire-panel.php
'admin' => [
    // ...
    'navbar_components' => [
        'left'  => ['quick-actions-button'],
        'right' => ['custom-notifications'],
    ],
],
```

| Position | Description |
|----------|-------------|
| `left` | Rendered after the page title, before the right-side actions |
| `right` | Rendered after notifications, before the user menu |

Each entry can be a **string** (component name) or an **array** with restrictions:

```php
'navbar_components' => [
    'left'  => [],
    'right' => [
        'public-widget',
        [
            'component'  => 'stop-impersonation',
            'visible'    => fn () => session()->has('impersonated_by'),
        ],
        [
            'component'  => 'admin-quick-menu',
            'permission' => 'admin.access',
            'roles'      => 'admin',
        ],
    ],
],
```

### Component entry properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `component` | string | yes | Livewire component name |
| `permission` | string | no | Required permission via PanelGate |
| `roles` | string\|array | no | Required role(s) |
| `visible` | bool\|callable\|string | no | Controls item visibility. Accepts `true`/`false`, a closure returning `bool`, or an invocable class name (config:cache compatible) |

---

## User menu (user dropdown)

The `user_menu` key in the panel configuration defines the items shown in the user dropdown in the navbar (next to the user's name).

If `user_menu` is empty (default), the dropdown shows the **Profile** item and the **Sign out** button. If you define items in `user_menu`, they replace the default Profile item. The **Sign out** button is always shown at the bottom and cannot be removed.

### Basic configuration

```php
// config/laravel-livewire-panel.php
'admin' => [
    // ...
    'user_menu' => [
        ['label' => 'Profile',  'route' => 'panel.admin.profile.index',   'icon' => 'user'],
        ['label' => 'Settings', 'route' => 'panel.admin.settings.index',  'icon' => 'cog-6-tooth'],
    ],
],
```

### Item properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `label` | string | yes | Visible text of the item |
| `route` | string | yes | Laravel route name. If the route does not exist, the item is hidden automatically |
| `icon` | string | no | Font Awesome icon name (e.g. `user`, `gear`, `bell`). Default: `layer-group` |
| `permission` | string | no | Required permission. Evaluated by the panel's gate driver |
| `roles` | string\|array | no | Required role(s). The user must have at least one |
| `type` | string | no | Use `'divider'` to insert a visual separator line |
| `visible` | bool\|callable\|string | no | Controls item visibility. Accepts `true`/`false`, a closure returning `bool`, or an invocable class name (config:cache compatible) |

### Dividers

You can insert visual separators between menu items:

```php
'user_menu' => [
    ['label' => 'Profile',  'route' => 'panel.admin.profile.index',  'icon' => 'user'],
    ['label' => 'Settings', 'route' => 'panel.admin.settings.index', 'icon' => 'cog-6-tooth'],
    ['type'  => 'divider'],
    ['label' => 'Activity Log', 'route' => 'panel.admin.activity.index', 'icon' => 'eye'],
],
```

### Permissions and roles

User menu items support the same `permission` and `roles` fields as sidebar navigation. Filtering rules are identical (see sections 5, 6, and 7).

```php
'user_menu' => [
    ['label' => 'Profile', 'route' => 'panel.admin.profile.index', 'icon' => 'user'],
    ['type'  => 'divider'],
    [
        'label'      => 'Admin Settings',
        'route'      => 'panel.admin.settings.index',
        'icon'       => 'cog-6-tooth',
        'permission' => 'settings.view',
        'roles'      => 'admin',
    ],
],
```

### Conditional visibility

Use the `visible` callback to show or hide items dynamically based on runtime conditions (e.g. session state, feature flags):

```php
'user_menu' => [
    ['label' => 'Profile', 'route' => 'panel.admin.profile.index', 'icon' => 'user'],
    [
        'label'   => 'Stop Impersonating',
        'route'   => 'impersonate.leave',
        'icon'    => 'user-slash',
        'visible' => fn () => session()->has('impersonated_by'),
    ],
],
```

The `visible` callback is evaluated at render time. If it returns `false`, the item is excluded from the menu. This works in both **config** and **modules** mode.

### Full override with custom component

If you need full control over the user dropdown (custom logic, custom HTML), you can generate a custom navbar:

```bash
php artisan panel:make-component navbar --panel=admin
```

This generates a class extending `AbstractNavbar` and a Blade view where you can freely modify the dropdown. Register the component in the config:

```php
'components' => [
    'navbar' => \App\Livewire\AdminNavbar::class,
],
```

The `$userMenu` variable (already filtered by permissions) is available in the custom component's view.
