# Localization

The panel includes a built-in language selector and uses Laravel's native translation system. All UI text in the panel is translatable, with translations provided for 10 languages out of the box.

## Enabling the language selector

Add the `locale` configuration to your panel:

```php
'locale' => [
    'enabled'      => true,
    'show_on_auth' => true,
    'available'    => [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
    ],
],
```

| Key | Type | Default | Description |
|---|---|---|---|
| `enabled` | bool | `false` | Show the language selector in the navbar |
| `show_on_auth` | bool | `false` | Show the language selector on login, register, and forgot password pages |
| `available` | array | `[...]` | Associative array of `locale_code => display_name` for available languages |

When enabled, a globe icon with the current language code and a dropdown arrow appears in the navbar. Users can switch languages from the dropdown.

## How it works

1. **Default locale** is read from `APP_LOCALE` in `.env` (Laravel's native behavior).
2. When a user selects a language, it is stored in the **session** (`panel_locale`).
3. On each request, the `SetPanelLocale` middleware reads the session value and applies `app()->setLocale()`.
4. If no language has been selected, Laravel uses the default from `.env`.

## Translation files

The panel uses two separate translation files per language:

| File | Purpose |
|---|---|
| `messages.php` | All UI text: auth pages, navbar, search, notifications, users module, dashboard |
| `sidebar.php` | Navigation labels for the sidebar menu |

## Included translations

The panel ships with translations in 10 languages:

| Code | Language |
|---|---|
| `en` | English |
| `es` | Español |
| `fr` | Français |
| `pt` | Português |
| `zh` | Chinese |
| `hi` | Hindi |
| `ar` | Arabic |
| `bn` | Bengali |
| `ru` | Russian |
| `ja` | Japanese |

## Translating sidebar menu items

All sidebar labels are automatically passed through Laravel's `__()` function. This means you can use translation keys directly in your navigation configuration.

### Built-in modules

When using `'mode' => 'modules'`, the built-in modules (Dashboard, Users) already use translation keys (`panel::sidebar.dashboard`, `panel::sidebar.users`). These are translated automatically.

### Config mode

When using `'mode' => 'config'`, use translation keys as labels in `sidebar_menu`:

```php
'sidebar_menu' => [
    ['label' => 'panel::sidebar.dashboard', 'route' => 'panel.admin.home', 'icon' => 'home'],
    ['label' => 'panel::sidebar.users', 'route' => 'panel.admin.users.index', 'icon' => 'users'],
    ['label' => 'panel::sidebar.settings', 'route' => 'panel.admin.settings', 'icon' => 'cog-6-tooth'],
],
```

The `sidebar.php` file includes common keys ready to use:

```
dashboard, users, settings, profile, logs, notifications, reports, analytics,
management, configuration, roles, permissions, categories, products, orders,
customers, messages, files, media, pages, posts, comments, tags, calendar,
tasks, support, help, home, system, tools, activity, audit
```

### Plain text labels

Plain text labels still work. If the label does not match any translation key, it is displayed as-is:

```php
'sidebar_menu' => [
    ['label' => 'Dashboard', 'route' => 'panel.admin.home', 'icon' => 'home'],
    ['label' => 'My Custom Page', 'route' => 'panel.admin.custom', 'icon' => 'star'],
],
```

### Custom sidebar translations

To add your own sidebar keys, create or publish `sidebar.php` for each language:

```
lang/vendor/panel/en/sidebar.php
lang/vendor/panel/es/sidebar.php
```

```php
// lang/vendor/panel/en/sidebar.php
return [
    'invoices' => 'Invoices',
    'billing'  => 'Billing',
];

// lang/vendor/panel/es/sidebar.php
return [
    'invoices' => 'Facturas',
    'billing'  => 'Facturacion',
];
```

Then in your config:

```php
'sidebar_menu' => [
    ['label' => 'panel::sidebar.invoices', 'route' => 'panel.admin.invoices', 'icon' => 'document-text'],
],
```

### User menu translations

The `user_menu` items also support translation keys the same way:

```php
'user_menu' => [
    ['label' => 'panel::sidebar.profile', 'route' => 'panel.admin.profile', 'icon' => 'user'],
    ['label' => 'panel::sidebar.settings', 'route' => 'panel.admin.settings', 'icon' => 'cog-6-tooth'],
],
```

## Overriding translations

Publish the language files:

```bash
php artisan vendor:publish --tag=panel-lang
```

This copies all translation files to `lang/vendor/panel/`. Edit any file to customize the text:

```php
// lang/vendor/panel/en/messages.php
return [
    'sign_in' => 'Log in',
    'sign_out' => 'Log out',
];
```

## Adding a new language

1. Create both files at `lang/vendor/panel/{code}/`:

```
lang/vendor/panel/de/messages.php
lang/vendor/panel/de/sidebar.php
```

2. Add the language to your panel config:

```php
'locale' => [
    'enabled'   => true,
    'available' => [
        'en' => 'English',
        'de' => 'Deutsch',
    ],
],
```

## Using the locale selector component

The panel provides a standalone Blade component `<x-panel::locale-selector />` that you can place anywhere in your views:

```blade
<x-panel::locale-selector />
```

This renders a dropdown with the globe icon and available languages. It works via a GET route (`/panel-locale/{code}`) that sets the session and redirects back. No Livewire required.

### Using in custom pages

```blade
<div style="display:flex;justify-content:flex-end;padding:1rem">
    <x-panel::locale-selector />
</div>
```

### Using in auth pages

Set `show_on_auth` to `true` in the locale config. The selector appears fixed at the top-right corner of login, register, and forgot password pages.

## Using translations in your own views

All panel translations use the `panel` namespace. You can reference them anywhere:

```blade
{{ __('panel::messages.sign_in') }}
{{ __('panel::sidebar.dashboard') }}
```

For your own application text, use Laravel's standard translation system by creating files in `lang/`:

```
lang/
  en/
    app.php       -> return ['welcome' => 'Welcome'];
  es/
    app.php       -> return ['welcome' => 'Bienvenido'];
```

Then in your views:

```blade
{{ __('app.welcome') }}
```

## Programmatic locale switching

You can switch locale programmatically via the route:

```
GET /panel-locale/{code}
```

This sets the session and redirects back. You can generate the URL with:

```php
route('panel.locale.switch', 'es')
```
