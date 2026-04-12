# Notifications

The package includes a complete notification system in the navbar: a bell icon with a live badge counter, a dropdown with notification items, mark-as-read actions, and automatic polling.

---

## How it works

1. The navbar renders a `PanelNotifications` Livewire component (bell icon + dropdown).
2. The component queries a **notification provider** registered for the current panel.
3. The provider returns the unread count and the notification items.
4. If `notification_polling` is enabled, the component re-fetches automatically at the configured interval.

No notifications appear until you register a provider. The bell icon is hidden entirely when `show_notifications` is `false` in the style config.

---

## Enabling notifications

In your style file (`config/laravel-livewire-panel/style_table.php` or your custom style):

```php
'navbar' => [
    'show_notifications'            => true,
    'notification_polling'          => true,
    'notification_polling_interval' => 30,
],
```

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `show_notifications` | bool | `true` | Show/hide the bell icon in the navbar |
| `notification_polling` | bool | `true` | Enable automatic refresh via `wire:poll` |
| `notification_polling_interval` | int | `30` | Polling interval in seconds |

---

## Creating a notification provider

Implement `NotificationProviderInterface`:

```php
namespace App\Notifications;

use AlpDevelop\LivewirePanel\Notifications\NotificationProviderInterface;

final class AppNotificationProvider implements NotificationProviderInterface
{
    public function count(string $panelId): int
    {
        return auth()->user()?->unreadNotifications()->count() ?? 0;
    }

    public function items(string $panelId, int $limit = 10): array
    {
        $notifications = auth()->user()
            ?->notifications()
            ->take($limit)
            ->latest()
            ->get() ?? collect();

        return $notifications->map(fn ($n) => [
            'id'    => $n->id,
            'title' => $n->data['title'] ?? '',
            'body'  => $n->data['body'] ?? '',
            'icon'  => $n->data['icon'] ?? 'bell',
            'color' => $n->data['color'] ?? '',
            'time'  => $n->created_at->diffForHumans(),
            'route' => $n->data['route'] ?? '',
            'read'  => $n->read_at !== null,
        ])->toArray();
    }

    public function markAsRead(string $id, string $panelId): void
    {
        auth()->user()?->notifications()->where('id', $id)->first()?->markAsRead();
    }

    public function markAllAsRead(string $panelId): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
    }
}
```

This example uses Laravel's built-in `DatabaseNotifications`. You can use any data source: Eloquent models, an API, Redis, etc.

---

## Registering the provider

Register your provider in a service provider's `boot()` method:

```php
use AlpDevelop\LivewirePanel\Notifications\NotificationRegistry;
use App\Notifications\AppNotificationProvider;

public function boot(): void
{
    app(NotificationRegistry::class)->register('admin', new AppNotificationProvider());
}
```

The first argument is the panel ID. Each panel can have its own provider:

```php
$registry = app(NotificationRegistry::class);
$registry->register('admin', new AdminNotificationProvider());
$registry->register('operator', new OperatorNotificationProvider());
```

---

## Registering from a plugin

Plugins can register notification providers in the `afterBoot()` hook:

```php
namespace App\Plugins;

use AlpDevelop\LivewirePanel\Notifications\NotificationRegistry;
use AlpDevelop\LivewirePanel\Plugins\AbstractPlugin;
use App\Notifications\AppNotificationProvider;

final class NotificationsPlugin extends AbstractPlugin
{
    public function id(): string
    {
        return 'notifications';
    }

    public function afterBoot(): void
    {
        app(NotificationRegistry::class)->register('admin', new AppNotificationProvider());
    }
}
```

---

## Notification item structure

Each item returned by `items()` must be an associative array with the following keys:

| Key | Type | Required | Description |
|-----|------|----------|-------------|
| `id` | string | yes | Unique identifier. Used by `markAsRead()` |
| `title` | string | yes | Notification title displayed in bold |
| `body` | string | no | Secondary text below the title |
| `icon` | string | no | Icon name from the [icon set](icons.md). Default: `bell` |
| `color` | string | no | Hex color (`#RRGGBB`) applied to the icon background. Only valid 6-digit hex codes are rendered |
| `time` | string | no | Relative time text (e.g. `"5 minutes ago"`) |
| `route` | string | no | URL to navigate to when the notification is clicked. Uses `wire:navigate` for SPA navigation |
| `read` | bool | no | `true` if the notification has been read. Unread items show a dismiss button and a visual highlight |

---

## Badge behavior

- When the provider returns `count > 0`, a red badge appears on the bell icon showing the count.
- Counts above 99 display as `99+`.
- When the count is 0, the badge is hidden and only the bell icon is shown.
- Polling keeps the badge updated in real time without full page reloads.

---

## User actions

| Action | Trigger | Method called |
|--------|---------|---------------|
| Click a notification | User clicks the item | Navigates to `route` (if set) |
| Dismiss one notification | Click the check icon on an unread item | `markAsRead($id, $panelId)` |
| Mark all as read | Click "Mark all as read" in the header | `markAllAsRead($panelId)` |

After `markAsRead` or `markAllAsRead`, the component re-renders automatically and the badge updates.

---

## Localization

Notification UI texts are defined in `panel::messages`:

| Key | Default (en) |
|-----|-------------|
| `notifications` | Notifications |
| `no_notifications` | No notifications |
| `mark_all_as_read` | Mark all as read |
| `mark_as_read` | Mark as read |

Override them by publishing the lang files or adding entries in your app's `lang/{locale}/panel/messages.php`. See [Localization](localization.md).

---

## Complete example

### 1. Laravel notification class

```php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class OrderReceived extends Notification
{
    use Queueable;

    public function __construct(private readonly int $orderId) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New order #' . $this->orderId,
            'body'  => 'A new order has been placed.',
            'icon'  => 'shopping-cart',
            'color' => '#198754',
            'route' => route('panel.admin.orders.show', $this->orderId),
        ];
    }
}
```

### 2. Dispatching the notification

```php
$user->notify(new OrderReceived($order->id));
```

### 3. Provider and registration

Use the `AppNotificationProvider` shown above and register it in `AppServiceProvider::boot()`. The notification will appear in the navbar dropdown with the shopping cart icon, green color, title, body, and a link to the order detail.

---

## Custom notification component

The default notification dropdown can be fully replaced with a custom Livewire component. This lets you add progress bars, dynamic content, or any custom layout.

### Generate the component

```bash
php artisan panel:make-component notifications --panel=admin
```

This creates:
- `app/Livewire/AdminNotifications.php` (extends `AbstractPanelNotifications`)
- `resources/views/livewire/admin-notifications.blade.php` (full Blade view copy)

### Register it in your panel config

```php
'components' => [
    'notifications' => \App\Livewire\AdminNotifications::class,
],
```

### Customize the view

The generated Blade view is a full copy of the default notification dropdown. Edit it freely to add progress bars, custom items, or any dynamic content. The parent class provides:

| Property / Method | Description |
|---|---|
| `$panelId` | Current panel ID |
| `$polling` | Whether polling is enabled |
| `$pollingInterval` | Polling interval in seconds |
| `$count` (via render) | Unread notification count |
| `$items` (via render) | Notification items array |
| `markAsRead($id)` | Mark a single notification as read |
| `markAllAsRead()` | Mark all notifications as read |

Override the `render()` method to add custom data:

```php
namespace App\Livewire;

use AlpDevelop\LivewirePanel\View\Livewire\AbstractPanelNotifications;
use Illuminate\Contracts\View\View;

final class AdminNotifications extends AbstractPanelNotifications
{
    protected function view(): string
    {
        return 'livewire.admin-notifications';
    }

    public function render(): View
    {
        $parentView = parent::render();
        $parentData = $parentView->getData();

        return view($this->view(), array_merge($parentData, [
            'pendingTasks' => auth()->user()?->tasks()->pending()->count() ?? 0,
        ]));
    }
}
```
