# Configuration Reference

## Full config example

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
            'navbar_components' => ['left' => [], 'right' => []],
            'back_to'   => null,

            'locale' => [
                'enabled'      => true,
                'show_on_auth' => true,
                'available'    => [
                    'en' => 'English',
                    'es' => 'Español',
                    'fr' => 'Français',
                ],
            ],

            'components' => [
                'login'                        => null,
                'register'                     => null,
                'forgot-password'              => null,
                'reset-password'               => null,
                'forgot-password-notification' => null,
                'sidebar'                      => null,
                'navbar'                       => null,
            ],

            'cdn' => [
                'chartjs' => [
                    'css'    => [],
                    'js'     => ['https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js'],
                    'routes' => [],
                ],
            ],

            'dashboard_stats' => [
                ['title' => 'Total Users', 'value' => '1,482', 'icon' => 'users', 'trend' => '+12 this month', 'trendType' => 'up'],
            ],
        ],

    ],

    'public_pages' => [],

];
```

---

## Panel config keys

| Key | Type | Default | Description |
|---|---|---|---|
| `id` | string | required | Unique identifier for the panel. Used internally and for module/widget registration |
| `prefix` | string | required | URL prefix. Panel lives at `/{prefix}/login`, `/{prefix}/dashboard`, etc. |
| `guard` | string | `'web'` | Laravel auth guard. Each panel can use a dedicated guard with its own user table |
| `theme` | string | `'bootstrap5'` | Visual theme: `bootstrap4`, `bootstrap5`, or `tailwind` |
| `customization` | string | `null` | Name of a style file in `config/laravel-livewire-panel/`. Controls CSS variable values for this panel |
| `middleware` | array | `['web', 'auth']` | Middleware applied to all authenticated routes. Auth routes (login/register) always use `['web']` |
| `gate` | string\|null | `null` | Gate driver for permission/role checks. Values: `'spatie'`, `'laravel'`, or `null` (no restrictions). See [Navigation](navigation.md) |
| `mode` | string | `'config'` | How sidebar and user menu items are sourced: `'modules'` (from module classes) or `'config'` (from `sidebar_menu` / `user_menu` arrays). See [Navigation](navigation.md) |
| `sidebar_menu` | array | `[]` | Sidebar items when `mode` is `'config'`. Supports items, categories with children, permissions and roles |
| `user_menu` | array | `[]` | User dropdown menu items when `mode` is `'config'`. Supports label, route, icon, permission, roles, visible and dividers. See [Navigation](navigation.md) |
| `navbar_components` | array | `['left'=>[],'right'=>[]]` | Livewire components injected into the navbar. Supports permission, roles and visible callback. See [Navigation](navigation.md) |
| `registration_enabled` | bool | `false` | Enables the `/register` route for this panel |
| `back_to` | string\|null | `null` | Panel ID shown as a "Back to X" link in the sidebar, for navigating between panels |
| `components` | array | built-ins | Override built-in Livewire components and notification. See [Components](components.md) |
| `cdn` | array | `[]` | Third-party CSS/JS libraries to load automatically, with optional per-route restrictions |
| `locale` | array | see below | Locale/language selector configuration. See [Localization](localization.md) |
| `dashboard_stats` | array | `[]` | Stats card data passed to `DashboardModule`. Each item has `title`, `value`, `icon`, `trend`, `trendType` |

### Root-level keys

| Key | Type | Default | Description |
|---|---|---|---|
| `default` | string | `'admin'` | Default panel ID used when resolving without explicit ID |
| `panels` | array | required | Associative array of panel configs keyed by panel ID |
| `public_pages` | array | `[]` | Public page routes registered outside any panel. Each entry has `route`, `component`, `middleware` |

---

## Public pages

Public pages are standalone pages that live outside any panel's authentication. Useful for landing pages, terms, pricing, etc.

Generate a page:

```bash
php artisan panel:make-page Pricing
```

Register it in `config/laravel-livewire-panel.php`:

```php
'public_pages' => [
    ['route' => '/pricing', 'component' => \App\Livewire\Pages\PricingPage::class],
    ['route' => '/terms', 'component' => \App\Livewire\Pages\TermsPage::class, 'middleware' => ['throttle:60,1']],
],
```

Each entry supports:

| Field | Type | Required | Description |
|---|---|---|---|
| `route` | string | yes | URL path for the page |
| `component` | string | yes | Livewire component class |
| `middleware` | array | no | Additional middleware to apply |

Public pages use the `panel::layouts.public` layout.

---

## Multi-panel setup

Multiple panels can coexist in the same application. Each panel is fully isolated: separate URL prefix, auth guard, theme, navigation, and CDN assets. A user authenticated in one panel is not automatically authenticated in another.

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
                ['label' => 'Users', 'route' => 'panel.admin.users.index', 'icon' => 'users'],
            ],
            'user_menu'  => [],
            'back_to'    => null,
            'components'  => ['login' => null, 'register' => null, 'forgot-password' => null, 'reset-password' => null, 'forgot-password-notification' => null, 'sidebar' => null, 'navbar' => null],
            'cdn'         => [],
        ],

        'operator' => [
            'id'                   => 'operator',
            'prefix'               => 'operator',
            'guard'                => 'operator',
            'theme'                => 'tailwind',
            'customization'        => null,
            'middleware'           => ['web', 'auth:operator'],
            'gate'                 => 'spatie',
            'registration_enabled' => false,
            'mode'                 => 'config',
            'sidebar_menu' => [
                ['label' => 'Dashboard', 'route' => 'panel.operator.dashboard.index', 'icon' => 'home'],
                ['label' => 'Tasks', 'route' => 'panel.operator.tasks.index', 'icon' => 'clipboard-document-list'],
            ],
            'user_menu'  => [],
            'back_to'    => 'admin',
            'components'  => ['login' => null, 'register' => null, 'forgot-password' => null, 'reset-password' => null, 'forgot-password-notification' => null, 'sidebar' => null, 'navbar' => null],
            'cdn'         => [],
        ],

    ],

];
```

In this example:

- `admin` uses the default `web` guard with Bootstrap 5, accessible at `/admin/login`
- `operator` uses a custom `operator` guard with Tailwind, accessible at `/operator/login`. It requires a separate `operators` table configured in `config/auth.php`
- `operator` has `back_to => 'admin'`, which shows a "Back to Admin" link in its sidebar
- Each panel has its own navigation, theme, and gate driver

---

## CDN assets

The `cdn` key lets you load third-party CSS/JS libraries automatically into a panel's layout without modifying Blade files.

Each entry in `cdn` is identified by an alias and has three fields:

| Field | Type | Description |
|---|---|---|
| `css` | array | CSS URLs to inject in `<head>` |
| `js` | array | JS URLs to inject before `</body>` with `data-navigate-once` (loaded once during SPA session) |
| `routes` | array | URL paths where the asset is loaded. Empty array = load on all routes in the panel |

The `@panelCdnLibraries` Blade directive is injected automatically in the panel layout. You do not need to add it manually.

All CDN and theme scripts are rendered with the `data-navigate-once` attribute. During Livewire SPA navigation (`wire:navigate`), scripts execute only on first encounter and remain in memory for the rest of the session.

Example — Chart.js on all routes, Select2 only on the users page:

```php
'cdn' => [
    'chartjs' => [
        'css'    => [],
        'js'     => ['https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js'],
        'routes' => [],
    ],
    'select2' => [
        'css'    => ['https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'],
        'js'     => [
            'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js',
            'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        ],
        'routes' => ['admin/users'],
    ],
],
```

---

## Guards

The `guard` key maps a panel to a Laravel auth guard defined in `config/auth.php`. This allows each panel to authenticate users from a different table or driver.

For example, an operator panel backed by its own Eloquent model:

```php
// config/auth.php
'guards' => [
    'operator' => ['driver' => 'session', 'provider' => 'operators'],
],
'providers' => [
    'operators' => ['driver' => 'eloquent', 'model' => App\Models\Operator::class],
],
```

```php
// config/laravel-livewire-panel.php
'operator' => [
    'id'    => 'operator',
    'guard' => 'operator',
    'theme' => 'tailwind',
    ...
],
```

The guard is used for login, logout and session checks throughout the panel.

---

## Access control

### Authentication vs authorization

The `middleware` key (e.g. `['web', 'auth']`) handles **authentication** — ensuring the user is logged in. `PanelAccessRegistry` handles **authorization** — ensuring the authenticated user is allowed to access a specific panel.

Without a registered gate, any authenticated user can access any panel. With a gate registered for a panel, `PanelAuthMiddleware` evaluates the callback on every request. If it returns `false`, the user is redirected to the panel's login page.

### Registering panel gates

In `AppServiceProvider::boot()`:

```php
use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;

public function boot(): void
{
    $access = $this->app->make(PanelAccessRegistry::class);

    $access->for('admin',    fn ($user) => $user->role === 'admin');
    $access->for('operator', fn ($user) => $user->role === 'operator');
}
```

The callback receives the currently authenticated user for the panel's guard and must return `true` or `false`.

### Using standard middleware instead

For simpler cases, standard Laravel middleware is enough — no `PanelAccessRegistry` required:

```php
'middleware' => ['web', 'auth', 'verified', 'throttle:60,1'],
```

---

## Style files

A style file controls the visual appearance of a panel through CSS variables — colors, sidebar width, navbar height, typography, and more.

Generate one with:

```bash
php artisan panel:make-style my-style
```

This creates `config/laravel-livewire-panel/my-style.php`. Reference it in the panel config:

```php
'customization' => 'my-style',
```

See [customization.md](customization.md) for all available CSS variables and dark/light mode support.

---

## Helper functions

```php
panel_route('admin', 'home');
panel_route('admin', 'users.index');
```

Generates a named route URL scoped to a panel. Equivalent to `route('panel.admin.home')`.

```php
panel_component('sidebar');
panel_component('login');
```

Resolves the Livewire class for the current panel's component. Returns the built-in class if no override is configured in `components`.

```php
to_panel('operator')->for($user)->redirect();
```

Redirects the current user into another panel, establishing a session in that panel's guard.
