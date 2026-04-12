# Components

## How it works

Each panel config has a `components` key. Set any key to a class name to replace the built-in component. Set it to `null` to keep the built-in.

```php
// config/laravel-livewire-panel.php
'components' => [
    'login'                        => \App\Livewire\Auth\AdminLogin::class,  // custom
    'register'                     => null,    // built-in RegisterComponent
    'forgot-password'              => null,    // built-in ForgotPasswordComponent
    'reset-password'               => null,    // built-in ResetPasswordComponent
    'forgot-password-notification' => null,    // built-in PanelForgotPasswordNotification
    'sidebar'                      => null,    // built-in Sidebar
    'navbar'                       => null,    // built-in Navbar
],
```

---

## Generating a custom component

Use the artisan command to generate the stub:

```bash
php artisan panel:make-component login           --panel=admin
php artisan panel:make-component register        --panel=admin
php artisan panel:make-component forgot-password  --panel=admin
php artisan panel:make-component reset-password   --panel=admin
php artisan panel:make-component forgot-password-notification --panel=admin
php artisan panel:make-component sidebar          --panel=admin
php artisan panel:make-component navbar           --panel=admin
```

Each command generates:
- A PHP class extending the correct abstract base
- A Blade view pre-filled with the built-in layout as a starting point
- A reminder of which config line to add

---

## Custom Login

### Generated class

```php
// app/Livewire/Auth/AdminLogin.php
namespace App\Livewire\Auth;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractLoginComponent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

#[Layout('panel::layouts.auth', ['title' => 'Admin Login'])]
final class AdminLogin extends AbstractLoginComponent
{
    public function render(): View
    {
        return view('livewire.auth.admin-login');
    }
}
```

The `AbstractLoginComponent` provides:
- `$email`, `$password`, `$remember`, `$panelId` public properties
- `mount()`: redirects if already authenticated
- `login()`: validates, attempts auth, regenerates session

You only need to provide the view.

### Registering

Register the Livewire component and add to config:

```php
// AppServiceProvider::boot()
\Livewire\Livewire::component('admin-login', AdminLogin::class);
```

```php
// config/laravel-livewire-panel.php
'components' => [
    'login' => \App\Livewire\Auth\AdminLogin::class,
],
```

---

## Custom Register

```php
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractRegisterComponent;

final class AdminRegister extends AbstractRegisterComponent
{
    public function render(): View
    {
        return view('livewire.auth.admin-register');
    }
}
```

`AbstractRegisterComponent` provides `$name`, `$email`, `$password`, `$password_confirmation`, and the full `register()` logic.

To enable registration on any panel, set `registration_enabled` to `true` in the panel config. This registers the `/register` route and shows a "Create account" link on the login page:

```php
'registration_enabled' => true,
```

If you use a custom login view, add the register link conditionally:

```blade
@if (Route::has("panel.{$panelId}.auth.register"))
    <a href="{{ route("panel.{$panelId}.auth.register") }}">
        {{ __('panel::messages.create_account') }}
    </a>
@endif
```

The link only renders when the register route exists (i.e., `registration_enabled` is `true`).

To also use a custom register component:

```php
'registration_enabled' => true,
'components' => [
    'register' => \App\Livewire\Auth\AdminRegister::class,
],
```

---

## Custom Forgot Password

```php
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractForgotPasswordComponent;

#[Layout('panel::layouts.auth', ['title' => 'Forgot password'])]
final class AdminForgotPassword extends AbstractForgotPasswordComponent
{
    public function render(): View
    {
        return view('livewire.auth.admin-forgot-password');
    }
}
```

`AbstractForgotPasswordComponent` provides `$email`, `$sent`, `$panelId`, and the full `submit()` logic that sends the reset link.

```php
'components' => [
    'forgot-password' => \App\Livewire\Auth\AdminForgotPassword::class,
],
```

---

## Custom Reset Password

```php
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractResetPasswordComponent;

#[Layout('panel::layouts.auth', ['title' => 'Reset password'])]
final class AdminResetPassword extends AbstractResetPasswordComponent
{
    public function render(): View
    {
        return view('livewire.auth.admin-reset-password');
    }
}
```

`AbstractResetPasswordComponent` provides:
- `$email`, `$password`, `$password_confirmation`, `$token`, `$panelId` properties
- `mount(string $token)`: reads token from route, email from query string
- `submit()`: validates, resets password via `Password::broker()->reset()`, redirects to login with success message

```php
'components' => [
    'reset-password' => \App\Livewire\Auth\AdminResetPassword::class,
],
```

---

## Custom Forgot Password Notification

The reset email is sent when a user submits the forgot-password form. To customize it:

```bash
php artisan panel:make-component forgot-password-notification --panel=admin
```

This generates:
- `app/Notifications/AdminForgotPasswordNotification.php` -- Notification class with overridable methods
- `resources/views/emails/admin-reset-password.blade.php` -- Email Blade template

### Notification class

The generated class extends `PanelForgotPasswordNotification` and exposes three methods:

```php
class AdminForgotPasswordNotification extends PanelForgotPasswordNotification
{
    protected function emailSubject(): string
    {
        return __('panel::messages.reset_password_title');
    }

    protected function emailView(): string
    {
        return 'emails.admin-reset-password';
    }

    protected function emailData(mixed $notifiable): array
    {
        return array_merge(parent::emailData($notifiable), [
            // Add custom variables here
        ]);
    }
}
```

| Method | Purpose |
|---|---|
| `emailSubject()` | Email subject line |
| `emailView()` | Blade view used for the email body |
| `emailData($notifiable)` | Variables passed to the view. `$notifiable` is the user model |

The parent `emailData()` provides `resetUrl` and `expireMinutes` by default.

### Registering

Add the notification class to your panel config:

```php
'components' => [
    'forgot-password-notification' => \App\Notifications\AdminForgotPasswordNotification::class,
],
```

---

## Custom Sidebar

```php
use AlpDevelop\LivewirePanel\View\Livewire\AbstractSidebar;

final class AdminSidebar extends AbstractSidebar
{
    protected function view(): string
    {
        return 'livewire.admin-sidebar';  // your Blade view
    }
}
```

`AbstractSidebar` provides:
- `$panelId`, `$activePath` public properties
- `mount()`: resolves current panel
- `render()`: passes `navItems`, `panelConfig`, `otherPanels` to the view

You can override `render()` entirely if you need additional data:

```php
public function render(): View
{
    return view('livewire.admin-sidebar', [
        'navItems'    => app(NavigationRegistry::class)->forPanel($this->panelId),
        'activePath'  => $this->activePath,
        'panelConfig' => app(PanelResolver::class)->resolveById($this->panelId),
        'otherPanels' => [],
        'customData'  => $this->loadCustomData(),
    ]);
}
```

---

## Custom Navbar

```php
use AlpDevelop\LivewirePanel\View\Livewire\AbstractNavbar;

final class AdminNavbar extends AbstractNavbar
{
    protected function view(): string
    {
        return 'livewire.admin-navbar';
    }
}
```

`AbstractNavbar` provides `$panelId`, `$title`, resolves user, logout route and profile route.

---

## Custom User Popover Header

The user popover header is the section inside the user dropdown that shows the avatar, name and email. You can replace it with a full Livewire component to add custom data, queries, or any PHP logic.

### Generate

```bash
php artisan panel:make-component user-popover-header --panel=admin
```

This generates:
- `app/Livewire/AdminUserPopoverHeader.php` -- Livewire component
- `resources/views/livewire/admin-user-popover-header.blade.php` -- Blade view

### Generated class

```php
// app/Livewire/AdminUserPopoverHeader.php
namespace App\Livewire;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Contracts\View\View;

final class AdminUserPopoverHeader extends Component
{
    #[Locked]
    public $user;

    public bool $showAvatar = true;
    public ?string $avatarUrl = null;

    public function render(): View
    {
        return view('livewire.admin-user-popover-header');
    }
}
```

The `$user` property is marked with `#[Locked]` to prevent client-side tampering via Livewire's hydration mechanism. The value is passed from the navbar and cannot be modified by the browser.

The component receives three properties from the navbar:

| Property | Type | Description |
|---|---|---|
| `$user` | Model (Locked) | The authenticated user object. Cannot be modified from the client |
| `$showAvatar` | bool | Whether the avatar should be shown (from config) |
| `$avatarUrl` | string\|null | Resolved avatar URL (from `avatar_resolver`) |

You can add any logic to the component:

```php
final class AdminUserPopoverHeader extends Component
{
    #[Locked]
    public $user;

    public bool $showAvatar = true;
    public ?string $avatarUrl = null;

    public function render(): View
    {
        return view('livewire.admin-user-popover-header', [
            'role'      => $this->user->roles->first()?->name ?? 'User',
            'lastLogin' => $this->user->last_login_at?->diffForHumans(),
            'company'   => $this->user->company?->name,
        ]);
    }
}
```

### Generated view

```blade
<div class="panel-sidebar-user-popover-header">
    @if ($showAvatar && $avatarUrl)
        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="panel-sidebar-avatar panel-sidebar-avatar--lg" style="object-fit:cover" />
    @elseif ($showAvatar)
        <span class="panel-sidebar-avatar panel-sidebar-avatar--lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
    @endif
    <div style="min-width:0">
        <div class="panel-sidebar-user-popover-name">{{ $user->name }}</div>
        <div class="panel-sidebar-user-popover-email">{{ $user->email }}</div>
    </div>
</div>
```

Modify the view freely. The available CSS classes for consistency:

| Class | Purpose |
|---|---|
| `panel-sidebar-user-popover-header` | Header container with flex layout |
| `panel-sidebar-avatar` | Avatar circle (32px) |
| `panel-sidebar-avatar--lg` | Large avatar (40px) |
| `panel-sidebar-user-popover-name` | User name text (bold) |
| `panel-sidebar-user-popover-email` | Secondary text (muted) |

### Registering

Add to your style config file (`config/laravel-livewire-panel/{style}.php`):

```php
'navbar' => [
    'user_popover_header_component' => 'admin-user-popover-header',
],
```

The value is the Livewire component name (kebab-case string). This is compatible with `config:cache` since it is a plain string.

---

## CSS variables with custom views

The CSS variables system works at the layout level, not inside individual components. When you create a custom sidebar or navbar, the same `var(--panel-*)` variables are available in its view:

```blade
<nav style="background: var(--panel-sidebar-bg); width: var(--panel-sidebar-width)">
    {{-- Your custom nav items --}}
</nav>
```

Using the panel CSS classes (`panel-sidebar`, `panel-nav-item`, etc.) gives you the full built-in styling automatically.

---

## Available abstract bases

| For | Extend |
|---|---|
| Login page | `AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractLoginComponent` |
| Register page | `AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractRegisterComponent` |
| Forgot password | `AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractForgotPasswordComponent` |
| Sidebar | `AlpDevelop\LivewirePanel\View\Livewire\AbstractSidebar` |
| Navbar | `AlpDevelop\LivewirePanel\View\Livewire\AbstractNavbar` |
