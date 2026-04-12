# Artisan Commands

## Install

Publishes config, views, and runs migrations.

```bash
php artisan panel:install
```

What it does:
- Publishes `config/laravel-livewire-panel.php`
- Publishes views to `resources/views/vendor/panel/`
- Creates example style files in `config/laravel-livewire-panel/`

---

## Make Component

Generates a custom Livewire component for login, register, forgot-password, reset-password, forgot-password-notification, sidebar, navbar, or notifications.

```bash
php artisan panel:make-component {type} [--panel=]
```

| Argument | Values |
|---|---|
| `type` | `login` \| `register` \| `forgot-password` \| `reset-password` \| `forgot-password-notification` \| `sidebar` \| `navbar` \| `notifications` |
| `--panel` | Panel ID (used as class name prefix, e.g. `admin` → `AdminLogin`) |

**Examples:**

```bash
php artisan panel:make-component login           --panel=admin
php artisan panel:make-component register        --panel=admin
php artisan panel:make-component forgot-password  --panel=admin
php artisan panel:make-component reset-password   --panel=admin
php artisan panel:make-component forgot-password-notification --panel=admin
php artisan panel:make-component sidebar          --panel=eventos
php artisan panel:make-component navbar           --panel=operator
php artisan panel:make-component notifications     --panel=admin
```

**Output for `login`, `register`, `forgot-password`, `reset-password`:**
- `app/Livewire/Auth/{Panel}{Type}.php`
- Corresponding Blade view
- Config snippet to copy

**Output for `sidebar`, `navbar`:**
- `app/Livewire/{Panel}{Type}.php`
- Corresponding Blade view

**Output for `notifications`:**
- `app/Livewire/{Panel}Notifications.php` (extends `AbstractPanelNotifications`)
- `resources/views/livewire/{panel}-notifications.blade.php` (full copy of default dropdown)
- Config snippet for `components.notifications`

See [Notifications](notifications.md) for customization details.

**Output for `forgot-password-notification`:**
- `app/Notifications/{Panel}ForgotPasswordNotification.php`
- `resources/views/emails/{panel}-reset-password.blade.php`
- Config snippet for `components.forgot-password-notification`

**Output for `user-popover-header`:**
- `app/Livewire/{Panel}UserPopoverHeader.php` (Livewire component with `$user`, `$showAvatar`, `$avatarUrl` public properties)
- `resources/views/livewire/{panel}-user-popover-header.blade.php`
- Config snippet for `navbar.user_popover_header_component`

The generated Livewire component receives the authenticated user object and avatar data as public properties. You can add any PHP logic, queries, or computed properties to customize the header content.

Add to your style config (`config/laravel-livewire-panel/{style}.php`):

```php
'navbar' => [
    'user_popover_header_component' => '{panel}-user-popover-header',
],
```

---

## Make Module

Generates a new module with routes, navigation, and a sample Livewire page.

```bash
php artisan panel:make-module {Name}
```

**Example:**

```bash
php artisan panel:make-module Inventario
```

**Output:**
- `app/Livewire/Modules/Inventario/InventarioModule.php`
- Sample page component and view

---

## Make Widget

Generates a new widget.

```bash
php artisan panel:make-widget {Name}
```

**Example:**

```bash
php artisan panel:make-widget SalesChart
```

**Output:**
- `app/Livewire/Widgets/SalesChart.php`
- `resources/views/livewire/widgets/sales-chart.blade.php`

---

## Make Page

Generates a public page (Livewire component + view).

```bash
php artisan panel:make-page {Name} [--middleware=*]
```

| Argument / Option | Description |
|---|---|
| `Name` | Page name in PascalCase (e.g. `Pricing`, `About`, `Terms`) |
| `--middleware` | Additional middleware (repeatable) |

**Example:**

```bash
php artisan panel:make-page Pricing
php artisan panel:make-page Terms --middleware=throttle
```

**Output:**
- `app/Livewire/Pages/{Name}Page.php`
- `resources/views/livewire/pages/{kebab-name}.blade.php`

Register the page in `config/laravel-livewire-panel.php`:

```php
'public_pages' => [
    ['route' => '/pricing', 'component' => \App\Livewire\Pages\PricingPage::class],
],
```

---

## Make Plugin

Generates a new plugin.

```bash
php artisan panel:make-plugin {Name}
```

**Example:**

```bash
php artisan panel:make-plugin Notifications
```

**Output:**
- `app/Plugins/NotificationsPlugin.php`
- Sample Livewire page and plugin class

---

## Make Style

Generates a new style (customization) file.

```bash
php artisan panel:make-style {name}
```

**Example:**

```bash
php artisan panel:make-style corporate
```

**Output:**
- `config/laravel-livewire-panel/corporate.php` with all available keys pre-populated

Reference in panel config:

```php
'customization' => 'corporate',
```

---

## Make Theme

Generates a new theme class.

```bash
php artisan panel:make-theme {Name}
```

**Example:**

```bash
php artisan panel:make-theme Bulma
```

**Output:**
- `app/Panel/Themes/BulmaTheme.php`

---

## Upgrade

Re-publishes updated assets from the package (skips files already customized).

```bash
php artisan panel:upgrade
```

---

## Quick reference

| Command | Description |
|---|---|
| `panel:install` | First-time setup |
| `panel:make-component {type} --panel={id}` | Custom login/register/forgot-password/sidebar/navbar/user-popover-header |
| `panel:make-module {Name}` | New module with routes and nav |
| `panel:make-widget {Name}` | New dashboard widget |
| `panel:make-page {Name}` | New public page |
| `panel:make-plugin {Name}` | New cross-panel plugin |
| `panel:make-style {name}` | New CSS variable style file |
| `panel:make-theme {Name}` | New CSS framework theme |
| `panel:upgrade` | Re-publish updated package assets |
