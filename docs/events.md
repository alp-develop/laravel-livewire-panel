# Audit Events

The package dispatches Laravel events for all critical actions. Listen to these events to build audit logs, send notifications, or trigger external integrations.

---

## Event list

All events extend `AlpDevelop\LivewirePanel\Events\PanelEvent` and include `panelId`, `ip`, and `timestamp` (ISO 8601).

| Event | Dispatched when | Properties |
|---|---|---|
| `LoginAttempted` | Login form submitted (success or failure) | `email`, `successful`, `guard` |
| `UserLoggedIn` | User authenticated successfully | `userId`, `guard` |
| `UserLoggedOut` | User logs out | `guard` |
| `UserRegistered` | New user registers via the panel | `userId`, `email`, `guard` |
| `UserCreated` | Admin creates a user via Users page | `userId`, `email`, `createdBy` |
| `UserUpdated` | Admin updates a user via Users page | `userId`, `updatedBy` |
| `UserDeleted` | Admin deletes a user via Users page | `userId`, `deletedBy` |
| `PanelAccessDenied` | User fails panel access check | `userId`, `reason` |

---

## Listening to events

Register listeners in your `EventServiceProvider` or use closures:

```php
use AlpDevelop\LivewirePanel\Events\LoginAttempted;
use AlpDevelop\LivewirePanel\Events\PanelAccessDenied;

Event::listen(LoginAttempted::class, function (LoginAttempted $event) {
    Log::channel('audit')->info('Login attempt', [
        'panel'      => $event->panelId,
        'email'      => $event->email,
        'successful' => $event->successful,
        'ip'         => $event->ip,
        'timestamp'  => $event->timestamp,
    ]);
});

Event::listen(PanelAccessDenied::class, function (PanelAccessDenied $event) {
    Log::channel('security')->warning('Access denied', [
        'panel'  => $event->panelId,
        'userId' => $event->userId,
        'reason' => $event->reason,
        'ip'     => $event->ip,
    ]);
});
```

---

## Base event class

All events share these properties from `PanelEvent`:

| Property | Type | Description |
|---|---|---|
| `panelId` | `string` | Panel ID where the action occurred |
| `ip` | `?string` | Client IP address (`null` when unavailable) |
| `timestamp` | `string` | ISO 8601 timestamp of the event |

---

## Event details

### LoginAttempted

| Property | Type | Description |
|---|---|---|
| `email` | `string` | Email address used in the attempt |
| `successful` | `bool` | Whether authentication succeeded |
| `guard` | `string` | Auth guard used |

### UserLoggedIn

| Property | Type | Description |
|---|---|---|
| `userId` | `int` | Authenticated user ID |
| `guard` | `string` | Auth guard used |

### UserLoggedOut

| Property | Type | Description |
|---|---|---|
| `guard` | `string` | Auth guard used |

### UserRegistered

| Property | Type | Description |
|---|---|---|
| `userId` | `int` | New user ID |
| `email` | `string` | Registered email |
| `guard` | `string` | Auth guard used |

### UserCreated

| Property | Type | Description |
|---|---|---|
| `userId` | `int` | Created user ID |
| `email` | `string` | User email |
| `createdBy` | `?int` | Admin user ID who created the user |

### UserUpdated

| Property | Type | Description |
|---|---|---|
| `userId` | `int` | Updated user ID |
| `updatedBy` | `?int` | Admin user ID who updated the user |

### UserDeleted

| Property | Type | Description |
|---|---|---|
| `userId` | `int` | Deleted user ID |
| `deletedBy` | `?int` | Admin user ID who deleted the user |

### PanelAccessDenied

| Property | Type | Description |
|---|---|---|
| `userId` | `?int` | User ID denied access (`null` if unauthenticated) |
| `reason` | `string` | Denial reason (default: `'unauthorized'`) |
