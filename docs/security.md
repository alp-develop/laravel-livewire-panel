# Security

This document covers the security features built into the package.

---

## Authentication

### Rate limiting

Login attempts are rate-limited to **5 attempts per 60 seconds** per IP address. After exceeding the limit, the user sees a throttle message with the remaining cooldown time.

The rate limiter uses Laravel's `RateLimiter` facade with the key `panel-login:{ip}`. Successful logins clear the counter.

### Session regeneration

Sessions are regenerated after every successful login to prevent session fixation attacks.

### Password validation

The Users page enforces strong passwords with `Password::min(8)->mixedCase()->numbers()`.

---

## Authorization

### Gate drivers

Permission and role checks are delegated to the configured gate driver per panel:

| Driver | Config value | Behavior |
|---|---|---|
| `SpatiGateDriver` | `'spatie'` | Uses `hasPermissionTo()` / `hasRole()` / `hasAnyRole()` |
| `LaravelGateDriver` | `'laravel'` | Uses `Gate::allows()` / `hasRole()` if method exists |
| `NullGateDriver` | `null` | Denies all permission and role checks (deny-by-default) |

### Panel access control

`PanelAccessRegistry` allows defining per-panel access callbacks. If a user fails the check, the middleware redirects them to an allowed panel or back to login. A `PanelAccessDenied` event is dispatched on denial.

### Guard switching

When redirecting a user to another panel with a different guard, the middleware validates that the user exists in the target provider before logging them in. Invalid cross-guard transfers are silently skipped.

---

## CSS injection prevention

All CSS values from the style configuration are sanitized before rendering. The `sanitizeCssValue()` method strips dangerous characters:

```
; { } \ < > " '
```

This prevents CSS injection through config values that could otherwise execute arbitrary styles or HTML. The sanitization is applied in:

- `AbstractTheme::cssVariables()` and `darkCssVariables()`
- `Bootstrap5Theme::cssVariables()` and `headHtml()` (light and dark mode)
- `Bootstrap4Theme::headHtml()` (light and dark mode)
- `TailwindTheme::headHtml()` (light and dark mode)

---

## SQL injection prevention

The `SearchQuerySanitizer` utility escapes SQL wildcards (`%`, `_`) and truncates input to a configurable maximum length. Use it in any module or plugin with search functionality:

```php
use AlpDevelop\LivewirePanel\Security\SearchQuerySanitizer;

$safe = SearchQuerySanitizer::sanitize($userInput);         // default 100 chars
$safe = SearchQuerySanitizer::sanitize($userInput, 50);     // custom limit

$users = User::where('name', 'like', "%{$safe}%")->get();
```

The built-in Users page uses this utility internally.

---

## Livewire property protection

Critical properties use Livewire's `#[Locked]` attribute to prevent client-side tampering:

- `AbstractLoginComponent::$panelId`
- `AbstractNavbar::$title`
- `UsersPage::$editingId`
- `UsersPage::$deletingId`

---

## Audit events

All critical actions dispatch Laravel events with IP address and timestamp for audit logging. See [Events](events.md) for the full list.

---

## Recommendations

For production deployments:

1. Always configure a gate driver (`'spatie'` or `'laravel'`) instead of `null`
2. Use `PanelAccessRegistry` to restrict which users can access each panel
3. Listen to `LoginAttempted` and `PanelAccessDenied` events for security monitoring
4. Use HTTPS to protect session cookies and credentials
5. Configure proper CORS headers if panels are accessed from different domains
