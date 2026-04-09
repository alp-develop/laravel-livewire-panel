# Icons

## Built-in icon component

The package includes a Blade component `<x-panel::icon>` that renders [Font Awesome 6](https://fontawesome.com/icons) icons (solid style by default). Font Awesome Free CSS is loaded automatically via CDN.

---

## Usage

```blade
<x-panel::icon name="house" size="20" />
<x-panel::icon name="users" size="16" class="text-muted" />
<x-panel::icon name="bell" size="24" style="color: red" />
```

| Attribute | Type | Required | Description |
|-----------|------|----------|-------------|
| `name` | string | yes | Font Awesome icon name (without `fa-` prefix) |
| `size` | string/int | yes | Font size in pixels |
| `class` | string | no | Additional CSS classes |
| `style` | string | no | Inline CSS styles |

The icon inherits the current text color automatically.

---

## Any Font Awesome icon

You can use **any** icon from [Font Awesome Free](https://fontawesome.com/search?o=r&m=free) by passing its name (without the `fa-` prefix):

```blade
<x-panel::icon name="database" size="18" />
<x-panel::icon name="cloud" size="20" />
<x-panel::icon name="code" size="16" />
<x-panel::icon name="file-pdf" size="20" />
```

All icons use `fa-solid` by default. The full Font Awesome 6 Free set (2,000+ icons) is available without any additional configuration.

---

## Legacy Heroicon names

For backward compatibility, the following Heroicon names are automatically mapped to their Font Awesome equivalents:

| Legacy name | Font Awesome name |
|-------------|-------------------|
| `home` | `house` |
| `x-mark` | `xmark` |
| `information-circle` | `circle-info` |
| `exclamation-triangle` | `triangle-exclamation` |
| `exclamation-circle` | `circle-exclamation` |
| `cog-6-tooth` | `gear` |
| `square-3-stack` | `layer-group` |
| `rectangle-stack` | `layer-group` |
| `arrow-trending-up` | `arrow-trend-up` |
| `arrow-trending-down` | `arrow-trend-down` |
| `currency-dollar` | `dollar-sign` |
| `shopping-cart` | `cart-shopping` |
| `arrow-right-on-rectangle` | `right-from-bracket` |
| `bars-3` | `bars` |
| `arrow-top-right-on-square` | `arrow-up-right-from-square` |
| `lock-closed` | `lock` |
| `globe-alt` | `globe` |

Icons with the same name in both libraries (`check`, `bell`, `user`, `users`, `eye`, `pencil`, `trash`, `plus`, `magnifying-glass`, `chevron-down`, `chevron-right`, `moon`, `sun`, `arrow-left`, `folder`, `language`, `chart-bar`, `ellipsis-vertical`, `arrow-right`) work without any changes.

---

## Customizing the icon component

Publish the package views:

```bash
php artisan vendor:publish --tag=panel-views
```

Then edit `resources/views/vendor/panel/components/icon.blade.php` to customize icon rendering. You can change the default style (e.g., `fa-regular` instead of `fa-solid`), add the legacy mapping, or replace with a different icon library entirely.
