# Visual Customization (CSS Variables)

## How it works

1. You create a style file in `config/laravel-livewire-panel/{name}.php`
2. You set `'customization' => '{name}'` in your panel config
3. The package reads the file and generates `var(--panel-*)` CSS variables injected into the layout's `<style>` tag
4. All built-in layouts and components use those variables

---

## Creating a style file

```bash
php artisan panel:make-style my-style
```

This creates `config/laravel-livewire-panel/my-style.php`.

Then reference it:

```php
// config/laravel-livewire-panel.php
'customization' => 'my-style',
```

---

## Full style file structure

```php
return [

    'id' => 'my-style',

    'sidebar' => [
        'initial_state'             => 'expanded',
        'collapsible'               => true,
        'icons_only_when_collapsed' => true,
        'persist_state'             => true,
        'overlay_on_mobile'         => true,
        'logo'                      => null,
        'logo_height'               => '40px',
        'logo_width'                => 'auto',
        'logo_class'                => '',
        'header_text'               => 'Panel Admin',
        'header_text_wrap'          => true,
        'show_user_menu'            => false,
        'show_avatar'               => true,
    ],

    'navbar' => [
        'sticky'                         => true,
        'show_search'                    => true,
        'show_notifications'             => true,
        'show_breadcrumbs'               => true,
        'show_user_menu'                 => true,
        'show_avatar'                    => true,
        'show_page_title'                => true,
        'notification_polling'           => true,
        'notification_polling_interval'  => 30,
    ],

    'theming' => [
        'font_family'   => 'Inter, sans-serif',
        'font_size'     => '14px',
        'border_radius' => '8px',

        'primary'   => '#4f46e5',
        'secondary' => '#6c757d',
        'success'   => '#198754',
        'danger'    => '#dc3545',
        'warning'   => '#ffc107',
        'info'      => '#0dcaf0',

        'sidebar' => [
            'width'           => '260px',
            'collapsed_width' => '64px',
            'item_font_size'  => '0.9rem',
            'item_font_weight' => '600',
            'light' => [
                'background'  => '#1e293b',
                'text'        => '#cbd5e1',
                'muted'       => '#64748b',
                'active_bg'   => null,
                'active_text' => '#ffffff',
            ],
            'dark' => [
                'background'  => '#0f172a',
                'text'        => '#94a3b8',
                'muted'       => '#475569',
                'active_bg'   => null,
                'active_text' => '#ffffff',
            ],
        ],

        'navbar' => [
            'height' => '60px',
            'light' => [
                'background' => '#ffffff',
                'text'       => '#1e293b',
                'border'     => '#e2e8f0',
            ],
            'dark' => [
                'background' => '#1e293b',
                'text'       => '#e2e8f0',
                'border'     => '#334155',
            ],
        ],

        'panel' => [
            'light' => [
                'primary'    => null,
                'background' => '#f4f6f9',
                'surface'    => '#ffffff',
                'border'     => '#e2e8f0',
                'text'       => '#333333',
                'text_muted' => '#6c757d',
            ],
            'dark' => [
                'primary'    => '#818cf8',
                'background' => '#0f172a',
                'surface'    => '#1e293b',
                'border'     => '#334155',
                'text'       => '#e2e8f0',
                'text_muted' => '#94a3b8',
            ],
        ],

        'auth' => [
            'light' => [
                'background' => '#f4f6f9',
            ],
            'dark' => [
                'background' => '#0f172a',
            ],
        ],
    ],

    'layout' => [
        'favicon'                => '/favicon.ico',
        'dark_mode'              => true,
        'dark_mode_show_on_auth' => false,
        'dark_mode_classes'      => [],
        'dark_mode_dispatch'     => null,
        'dark_mode_callback'     => null,
        'page_transition'    => 'fade',
        'back_to_top'        => true,
        'content_max_width'  => null,
        'avatar_resolver'    => null,
    ],

];
```

---

## Sidebar options

| Key | Type | Default | Description |
|---|---|---|---|
| `initial_state` | string | `'expanded'` | Sidebar state on first visit: `'expanded'` or `'collapsed'` |
| `collapsible` | bool | `true` | Whether the sidebar can be collapsed |
| `icons_only_when_collapsed` | bool | `true` | Show only icons when collapsed |
| `persist_state` | bool | `true` | Remember collapsed/expanded state in localStorage |
| `overlay_on_mobile` | bool | `true` | Sidebar overlays content on small screens |
| `logo` | string\|null | `null` | Path to logo image (e.g. `'/images/logo.png'`). `null` = no logo |
| `logo_height` | string | `'40px'` | CSS height value for the logo |
| `logo_width` | string | `'auto'` | CSS width value for the logo |
| `logo_class` | string | `''` | Additional CSS classes applied to the logo `<img>` |
| `header_text` | string | `'Panel Admin'` | Text shown in the sidebar header (below or instead of logo) |
| `header_text_wrap` | bool | `true` | Whether sidebar header text wraps to the next line |
| `show_user_menu` | bool | `false` | Show user menu popover at the bottom of the sidebar |
| `show_avatar` | bool | `true` | Show user avatar in the sidebar user section |

### Sidebar state behavior

The sidebar state system uses a combination of `initial_state`, `persist_state`, `collapsible` and `icons_only_when_collapsed` to control how the sidebar behaves across page loads and SPA navigations.

**First visit (no stored preference):**
- The sidebar renders in the state defined by `initial_state` (`'expanded'` or `'collapsed'`).
- If `persist_state` is `true`, the user's first manual toggle writes the preference to `localStorage`.

**Subsequent visits:**
- If `persist_state` is `true` and a stored preference exists, the stored value takes priority over `initial_state`.
- If `persist_state` is `false`, `initial_state` is always used regardless of any stored value.

**Collapsibility:**
- When `collapsible` is `false`, the toggle button on desktop does nothing. The sidebar stays in its `initial_state`.
- On mobile, the hamburger button always works (opens/closes the overlay sidebar) regardless of `collapsible`.

**Icons only:**
- When `icons_only_when_collapsed` is `true` (default), collapsing the sidebar hides text labels and shows only icons.
- When `icons_only_when_collapsed` is `false`, the sidebar still shrinks to `collapsed_width` but labels remain visible (useful for narrow sidebars with short labels).

**SPA navigation:**
- The sidebar state is preserved across Livewire SPA navigations (`wire:navigate`). The `livewire:navigating` `onSwap` callback re-applies the correct state from `localStorage` before the browser paints, preventing any visual flash.

---

## Navbar options

| Key | Type | Default | Description |
|---|---|---|---|
| `sticky` | bool | `true` | Navbar stays fixed at the top when scrolling |
| `show_search` | bool | `true` | Show search input in navbar |
| `show_notifications` | bool | `true` | Show notification bell icon |
| `show_breadcrumbs` | bool | `true` | Show breadcrumbs bar |
| `show_user_menu` | bool | `true` | Show user dropdown in navbar |
| `show_avatar` | bool | `true` | Show user avatar in the navbar user dropdown |
| `show_page_title` | bool | `true` | Show current page title in the navbar |
| `notification_polling` | bool | `true` | Enable automatic polling for new notifications |
| `notification_polling_interval` | int | `30` | Polling interval in seconds |

---

## Layout options

| Key | Type | Default | Description |
|---|---|---|---|
| `favicon` | string\|null | `null` | Path to favicon (e.g. `'/favicon.ico'`). Renders `<link rel="icon">` in `<head>` to prevent re-fetch during SPA navigation |
| `dark_mode` | bool | `true` | Enable dark mode toggle in navbar |
| `dark_mode_show_on_auth` | bool | `false` | Show dark mode toggle on login, register and forgot password pages |
| `dark_mode_classes` | array | `[]` | Additional CSS classes applied to `<html>` in dark mode |
| `dark_mode_dispatch` | string\|null | `null` | Livewire event dispatched on theme change |
| `dark_mode_callback` | string\|null | `null` | JS callback executed on theme change |
| `page_transition` | string | `'fade'` | Page transition animation: `'fade'` or `null`. Triggers after Livewire component hydration |
| `back_to_top` | bool | `true` | Show back-to-top button when scrolling down |
| `content_max_width` | string\|null | `null` | CSS max-width for content area (e.g. `'1400px'`). `null` = full width |
| `avatar_resolver` | Closure\|null | `null` | Callback `fn($user) => string` returning avatar URL. `null` = default initials avatar |

---

## Theming system

All visual settings are centralized in `theming`. It has three subsections (`sidebar`, `navbar`, `panel`), each with its own `light` and `dark` color sets, plus shared typography and semantic colors at the root.

### Root (shared across modes)

| Parameter | What it controls |
|---|---|
| `font_family` | Global font family |
| `font_size` | Base font size |
| `border_radius` | Border radius for cards, inputs, buttons, badges |
| `primary` | Base accent color. Can be overridden per mode in `panel.light.primary` / `panel.dark.primary` |
| `secondary` | Secondary buttons and badges |
| `success` | Success states |
| `danger` | Error states, delete buttons |
| `warning` | Warning states |
| `info` | Informational badges and alerts |

### Sidebar (`theming.sidebar`)

Structural settings plus per-mode colors.

| Parameter | What it controls |
|---|---|
| `width` | Sidebar width when expanded |
| `collapsed_width` | Sidebar width when collapsed |
| `item_font_size` | Font size for navigation items |
| `item_font_weight` | Font weight for navigation items |

**`theming.sidebar.light` / `theming.sidebar.dark`:**

| Parameter | What it controls |
|---|---|
| `background` | Sidebar background color |
| `text` | Navigation item text color |
| `muted` | Section header / muted text color |
| `active_bg` | Active item background. `null` = falls back to `theming.primary` |
| `active_text` | Active item text color |

### Navbar (`theming.navbar`)

| Parameter | What it controls |
|---|---|
| `height` | Navbar height |

**`theming.navbar.light` / `theming.navbar.dark`:**

| Parameter | What it controls |
|---|---|
| `background` | Navbar background color |
| `text` | Navbar text/icon color |
| `border` | Navbar bottom border color |

### Panel (`theming.panel`)

**`theming.panel.light` / `theming.panel.dark`:**

| Parameter | What it controls |
|---|---|
| `primary` | Per-mode primary override. `null` = uses `theming.primary`. Common use: lighter shade for dark mode |
| `background` | Page background behind content |
| `surface` | Card, modal, dropdown backgrounds |
| `border` | Card, table, input border color |
| `text` | Primary text color |
| `text_muted` | Secondary text color (labels, timestamps) |

### Auth (`theming.auth`)

Background color for auth pages (login, register, forgot password). Separate from the panel content background.

**`theming.auth.light` / `theming.auth.dark`:**

| Parameter | What it controls |
|---|---|
| `background` | Auth page background color |

### Quick examples

Blue corporate sidebar:
```php
'theming' => [
    'primary' => '#1d4ed8',
    'sidebar' => [
        'light' => [
            'background'  => '#1e3a5f',
            'text'        => '#bfdbfe',
            'muted'       => '#6b7280',
            'active_bg'   => '#1d4ed8',
            'active_text' => '#ffffff',
        ],
    ],
],
```

Green nature dark panel:
```php
'theming' => [
    'panel' => [
        'dark' => [
            'primary'    => '#4ade80',
            'background' => '#052e16',
            'surface'    => '#14532d',
            'border'     => '#166534',
            'text'       => '#dcfce7',
            'text_muted' => '#86efac',
        ],
    ],
],
```

---

## Available CSS variables

These are generated from your style file and available anywhere in your views:

| Variable | Source (light mode) | Source (dark mode) |
|---|---|---|
| `--panel-primary` | `panel.light.primary` or `theming.primary` | `panel.dark.primary` or `theming.primary` |
| `--panel-secondary` | `theming.secondary` | (same) |
| `--panel-success` | `theming.success` | (same) |
| `--panel-danger` | `theming.danger` | (same) |
| `--panel-warning` | `theming.warning` | (same) |
| `--panel-info` | `theming.info` | (same) |
| `--panel-content-bg` | `panel.light.background` | `panel.dark.background` |
| `--panel-card-bg` | `panel.light.surface` | `panel.dark.surface` |
| `--panel-card-border` | `panel.light.border` | `panel.dark.border` |
| `--panel-text-primary` | `panel.light.text` | `panel.dark.text` |
| `--panel-text-muted` | `panel.light.text_muted` | `panel.dark.text_muted` |
| `--panel-auth-bg` | `auth.light.background` | `auth.dark.background` |
| `--panel-navbar-bg` | `navbar.light.background` | `navbar.dark.background` |
| `--panel-navbar-text` | `navbar.light.text` | `navbar.dark.text` |
| `--panel-navbar-border` | `navbar.light.border` | `navbar.dark.border` |
| `--panel-sidebar-bg` | `sidebar.light.background` | `sidebar.dark.background` |
| `--panel-sidebar-text` | `sidebar.light.text` | `sidebar.dark.text` |
| `--panel-sidebar-muted` | `sidebar.light.muted` | `sidebar.dark.muted` |
| `--panel-sidebar-active-bg` | `sidebar.light.active_bg` or `--panel-primary` | `sidebar.dark.active_bg` or `--panel-primary` |
| `--panel-sidebar-active-text` | `sidebar.light.active_text` | `sidebar.dark.active_text` |
| `--panel-sidebar-width` | `sidebar.width` | (same) |
| `--panel-sidebar-collapsed` | `sidebar.collapsed_width` | (same) |
| `--panel-sidebar-item-size` | `sidebar.item_font_size` | (same) |
| `--panel-sidebar-item-weight` | `sidebar.item_font_weight` | (same) |
| `--panel-navbar-height` | `navbar.height` | (same) |
| `--panel-font` | `theming.font_family` | (same) |
| `--panel-font-size` | `theming.font_size` | (same) |
| `--panel-radius` | `theming.border_radius` | (same) |

You can use any of these in your custom views or components:

```css
.my-custom-element {
    background: var(--panel-card-bg);
    color: var(--panel-text-primary);
    border: 1px solid var(--panel-card-border);
    border-radius: var(--panel-radius);
}
```

---

## Dark mode toggle

When `layout.dark_mode` is `true`, a moon/sun toggle icon appears in the navbar between the search and notification icons.

When `layout.dark_mode_show_on_auth` is also `true`, the toggle appears on auth pages (login, register, forgot password) as a standalone button at the top-right corner.

You can also use `<x-panel::dark-mode-toggle />` in any Blade view to render the toggle independently.

- Clicking the icon switches between the `light` and `dark` variable sets for sidebar, navbar, and panel
- The preference is persisted in `localStorage` (`panel-dark-mode`)
- Bootstrap 5 panels also get `data-bs-theme="dark"` applied automatically
- Each theme (BS4, BS5, Tailwind) generates framework-specific overrides for forms, modals, tables, and dropdowns

---

## Available layouts

| Layout | Sidebar | Navbar | CSS loaded | Auth required |
|---|---|---|---|---|
| `panel::layouts.app` | Yes | Yes | Theme + `panel-app.css` | Yes |
| `panel::layouts.auth` | No | No | Theme + `panel-auth.css` (centered) | No |
| `panel::layouts.auth-base` | No | No | Theme + `panel-auth.css` (no container) | No |
| `panel::layouts.blank` | No | No | Theme + `panel-base.css` (free body) | No |
| `panel::layouts.public` | No | Topbar | Theme + `panel-public.css` | No |

### Blank layout

`panel::layouts.blank` provides a minimal HTML shell with theme CSS variables, locale selector and dark mode toggle (based on your config) but no sidebar, navbar or forced body styles.

Use it for pages that need shared session and panel theming but full control over the layout:

```php
#[Layout('panel::layouts.blank', ['title' => 'My Page'])]
final class MyPage extends Component
{
    public function render()
    {
        return view('livewire.my-page');
    }
}
```

Register the route with `PanelAuthMiddleware` to require authentication:

```php
Route::middleware(['web', PanelAuthMiddleware::class])
    ->get('/my-page', MyPage::class);
```
