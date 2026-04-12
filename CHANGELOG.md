# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2] - 2026-04-12

### Added

- `docs/notifications.md`: Dedicated documentation for the notification system. Covers enabling notifications, creating and registering a `NotificationProviderInterface`, item structure, badge behavior, user actions, localization keys, plugin registration, and a complete example with Laravel `DatabaseNotifications`.
- Cross-references to `docs/notifications.md` from `README.md`, `docs/api-reference.md`, and `docs/customization.md`.

## [1.0.1] - 2026-04-12

### Added

- `user-popover-header` type for `panel:make-component` command. Generates a full Livewire component (`{Panel}UserPopoverHeader`) and Blade view to completely customize the user popover header in the navbar dropdown.
- `user_popover_header_component` option in style config (`navbar` section). Accepts a Livewire component name (string) to replace the default user popover header. Compatible with `config:cache`.
- `.panel-navbar-spacer` CSS class: Flex spacer element between custom navbar components and the default action icons (search, dark mode, locale, notifications, user menu).
- Navbar custom components positioning: `navbar_components.left` renders after the page title, on the left side. `navbar_components.right` renders after the spacer, before the default action icons.

### Fixed

- Navbar mobile overflow: On screens <= 768px, the page title is now hidden, padding and gaps are reduced, the locale selector hides its globe icon (shows only the language code), the user name is hidden, and the user trigger is compacted. All elements stay in a single line.
- Navbar mobile z-index: Dropdowns and popovers now render correctly above other content on mobile (removed `overflow: hidden` that was clipping them).
- Favicon `<link>` now includes `data-navigate-once` to prevent re-fetching on every SPA navigation.

### Changed

- `.panel-navbar-title` no longer has `flex: 1`. A dedicated `.panel-navbar-spacer` element now handles the flex expansion between custom components and the default action icons.
- Navbar template structure reordered: `[hamburger] [title] [left components] [spacer] [right components] [default actions]`.

## [1.0.0] - 2026-04-11

### Added

Core

- `PanelKernel`: Boots all panel systems — themes, modules, plugins, navigation, search, widgets.
- `PanelResolver`: Resolves active panel from request path and validates panel access by ID.
- `PanelContext`: Stores active panel ID in request context for component access.
- `PanelRenderer`: Renders CSS variables, theme assets, CDN libraries via Blade directives.
- `PanelPortalBuilder`: Builds panel-specific route URLs and navigation paths.
- `LivewirePanelServiceProvider`: Registers all singletons, commands, Blade components, middleware.
- Multi-panel support: Multiple independent panels (e.g. admin, operator) in a single application with separate config, theme, guard, gate and navigation per panel.

Auth System

- `PanelGate`: Permission and role checking with pluggable drivers.
- `PanelAccessRegistry`: Custom access checkers per panel via closure callbacks.
- `GateDriverInterface`: Contract for permission checking implementations.
- `LaravelGateDriver`: Uses Laravel's native Gate facade for permission checks.
- `SpatiGateDriver`: Uses spatie/laravel-permission for role and permission checks.
- `NullGateDriver`: Deny-by-default driver when no gate is configured.
- `PanelAuthMiddleware`: Verifies authentication, resolves panel context, handles guard routing with cross-guard validation.
- Rate limiting on login attempts (5 per 60s per email+IP).
- Rate limiting on registration attempts (5 per 60s per IP).
- Rate limiting on forgot-password attempts (5 per 60s per IP).
- Rate limiting on reset-password attempts (5 per 60s per IP).
- Anti-email-enumeration on forgot-password: Always shows success message regardless of email existence.
- Token validation on reset-password mount: Rejects consumed or invalid tokens with redirect to login.

Theme System

- `ThemeInterface` and `AbstractTheme`: Base contracts and default CSS variable generation with color and layout theming.
- `Bootstrap5Theme`: Bootstrap 5.3 implementation with CDN assets. Buttons follow configured primary color on all states.
- `Bootstrap4Theme`: Bootstrap 4 implementation with CDN assets.
- `TailwindTheme`: Tailwind CSS implementation.
- `ThemeRegistry`: Lazy registration and resolution of themes.
- CSS custom properties (`--panel-*`): Full color and layout theming via variables.
- `dark_mode_show_on_auth`: Config option to show dark mode toggle on auth pages.

Module System

- `ModuleInterface` and `AbstractModule`: Base contracts with `id()`, `navigationItems()`, `userMenuItems()`, `routes()`, `publicRoutes()`, `permissions()`.
- `ModuleRegistry`: Stores and retrieves modules per panel.
- `AuthModule`: Login, register, forgot-password, reset-password routes with customizable components.
- `AbstractResetPasswordComponent`: Base component for password reset form with token validation and rate limiting.
- `ResetPasswordComponent`: Concrete reset-password page with auth layout.
- `PanelForgotPasswordNotification`: Customizable email notification with overridable subject, view and data methods.
- Password reset email template with themed HTML layout and action button.
- Register link on login page when `registration_enabled` is true.
- `DashboardModule`: Dashboard home route with configurable dashboard stats.
- `UsersModule`: User management with search, pagination, edit and delete. Permission-gated CRUD operations.
- `NavigationItem`: Navigation item with label, route, icon, permission, roles, badge, description and keywords.
- `NavigationGroup`: Grouped navigation items with parent label and collapsible children.

Widget System

- `WidgetInterface` and `AbstractWidget`: Livewire-based widgets with `canView()`, title, polling support.
- `WidgetRegistry`: Registers and retrieves widgets by alias.
- `StatsCardWidget`: Metric display with value, icon, trend (up/down/neutral), color.
- `ChartWidget`: Chart.js integration (line, bar, etc.) with lazy loading.
- `RecentTableWidget`: Data table widget with headers, rows, limit, empty message.

Navigation System

- `NavigationRegistry`: Stores navigation items per panel from modules or config.
- Config mode: Static sidebar menu loaded from `sidebar_menu` config key with groups, routes and permissions.
- Modules mode: Dynamic navigation from `navigationItems()` on registered modules.
- Permission filtering: Navigation items filtered by user permissions and roles.
- User menu items: Separate user dropdown menu populated by modules.
- Categories and subcategories: Collapsible groups with Alpine.js `x-collapse`.

Search System

- `SearchProviderInterface`: Contract for custom search sources with category, icon, search method.
- `NavigationSearchProvider`: Built-in provider indexing navigation items with permission filtering.
- `SearchRegistry`: Registers providers per panel, performs indexed search.
- `PanelSearch`: Livewire component for global search querying all registered providers.

Plugin System

- `PluginInterface` and `AbstractPlugin`: Lifecycle hooks `beforeBoot()` and `afterBoot()`, `registerNavigation()`, `registerWidgets()`.
- `PluginRegistry`: Boots plugins in order with lifecycle hook execution.

Notification System

- `NotificationProviderInterface`: Contract with `count()`, `items(limit)`, `markAsRead(id)`, `markAllAsRead()`.
- `NotificationRegistry`: Stores one notification provider per panel.
- `PanelNotifications`: Livewire component with polling, count badge and notification list.

Audit Events System

- `PanelEvent`: Abstract base event with panelId, IP address and timestamp.
- `LoginAttempted`: Dispatched on login attempt with email, success status and guard.
- `UserLoggedIn`: Dispatched on successful login with userId and guard.
- `UserLoggedOut`: Dispatched on logout with guard.
- `UserRegistered`: Dispatched on user registration with userId, email and guard.
- `UserCreated`: Dispatched on user creation via UsersPage with userId, email and createdBy.
- `UserUpdated`: Dispatched on user update via UsersPage with userId and updatedBy.
- `UserDeleted`: Dispatched on user deletion via UsersPage with userId and deletedBy.
- `PanelAccessDenied`: Dispatched when panel access is denied with userId and reason.

Livewire Components

- `Sidebar` and `AbstractSidebar`: Navigation sidebar with collapsible state, user avatar, logo, header text. Configurable `initial_state` (expanded/collapsed), `persist_state` via `localStorage`, `collapsible` toggle control, `icons_only_when_collapsed` mode, and `overlay_on_mobile`. State preserved across SPA navigations via `livewire:navigating` `onSwap` callback (zero-flash).
- `Navbar` and `AbstractNavbar`: Top navigation bar with page title, user dropdown, logout, notifications. Title property locked against client tampering.
- Menu item `visible` property supports `bool`, `callable` (closure) and invocable class string for `config:cache` compatibility.
- `LoginComponent`: Login form with email/password, validation, remember-me.
- `RegisterComponent`: Registration form with validation (if enabled).
- `ForgotPasswordComponent`: Password reset request form with rate limiting and anti-enumeration.
- `ResetPasswordComponent`: Password reset form with token validation and rate limiting.
- `DashboardPage`: Dashboard with configurable stats.
- `UsersPage`: User management with search, pagination, CRUD. Permission checks on all operations.

Blade Layouts

- `panel::layouts.app`: Main authenticated layout with sidebar, navbar, theme assets.
- `panel::layouts.auth`: Auth page layout for login, register, forgot-password. Dynamic background via `var(--panel-auth-bg)`.
- `panel::layouts.auth-base`: Bare auth layout for custom components allowing full control over the auth container and brand section.
- `panel::layouts.blank`: Minimal layout without sidebar or navbar. Loads theme CSS, CSS variables, locale selector and dark mode toggle. Body is completely free.
- `panel::layouts.public`: Public page layout for unauthenticated pages outside the panel.

Blade Components

- `<x-panel::alert />`: Themed alert component with variant and dismissible props.
- `<x-panel::button />`: Themed button with variant, type and size props.
- `<x-panel::card />`: Themed card wrapper with title prop.
- `<x-panel::icon />`: Font Awesome 6 Free icon system with name, size and class props.
- `<x-panel::portal />`: Route builder for cross-panel links with panel, route and params props.
- `<x-panel::locale-selector />`: Standalone language selector dropdown for use in any view (auth pages, custom pages).
- `<x-panel::dark-mode-toggle />`: Standalone dark mode toggle button for use in any view (auth pages, custom pages).

Localization

- Built-in translations for 10 languages (en, es, fr, pt, zh, hi, ar, bn, ru, ja).
- Two translation files per language: `messages.php` (UI text) and `sidebar.php` (navigation labels).
- Language selector in navbar with globe icon and dropdown.
- `SetPanelLocale` middleware: Reads locale from session and applies to app. Registered via Kernel for compatibility with packages that sync middleware groups (e.g. Sanctum).
- `LocaleController`: GET route for switching locale from non-Livewire contexts.
- `locale.show_on_auth` config option to show language selector on auth pages.
- Sidebar labels automatically support translation keys via `__()`.
- Publishable language files via `--tag=panel-lang`.

Blade Directives

- `@panelCssVars`: Outputs CSS custom properties for theming.
- `@panelCssAssets`: Outputs theme CSS links, CDN CSS and Font Awesome 6.5.2.
- `@panelJsAssets`: Outputs theme JS scripts and CDN JS.
- `@panelCdnLibraries`: Outputs CDN assets for the current route.

CDN System

- `CdnManager`: Static registry for CDN libraries with route-based activation.
- `CdnPluginResolver`: Resolves which CDN assets apply to the current request path.
- Built-in CDN support for Chart.js, SweetAlert2, Select2 (with jQuery Slim) and Flatpickr.
- CDN and theme scripts rendered with `data-navigate-once` for SPA performance: libraries load once and stay in memory across navigations.

Config System

- `PanelConfig`: Reads multiple panels, default panel, per-panel settings (id, prefix, guard, theme, mode, gate, registration, components, dashboard_stats, cdn).
- `PanelStyleConfig`: Loads PHP style files from `config/laravel-livewire-panel/` for customization.
- `theming.auth` config section with `light.background` and `dark.background` for configurable auth page backgrounds.
- `layout.favicon` config option for explicit favicon link in `<head>`, preventing re-fetch during SPA navigations.
- `layout.page_transition` with JavaScript-driven fade: transition triggers after Livewire component hydration for seamless page changes.

Artisan Commands

- `panel:install`: Interactive setup with config, styles and optional view publishing. Supports `--defaults` and `--force` flags.
- `panel:make-module {name}`: Generates module class, page component and view.
- `panel:make-widget {name}`: Generates Livewire widget class and Blade view.
- `panel:make-theme {name}`: Generates custom theme class extending AbstractTheme.
- `panel:make-plugin {name}`: Generates plugin class with lifecycle hooks.
- `panel:make-style {name}`: Generates customization file in `config/laravel-livewire-panel/`.
- `panel:make-page {name}`: Generates public Livewire page outside auth.
- `panel:make-component {type}`: Generates customizable Livewire components. Types: login, register, forgot-password, reset-password, forgot-password-notification, sidebar, navbar.

Helper Functions

- `panel_route($panelId, $name, $params)`: Returns route URL for a panel.
- `to_panel($panelId)`: Returns `PanelPortalBuilder` instance.
- `panel_component($key)`: Resolves component class (login, register, sidebar, navbar).

Livewire Compatibility

- `LivewireCompat`: Abstraction layer for Livewire v3 and v4 differences (page routes, component registration).
- `LivewireVersion`: Detects major version from `Livewire::VERSION` or Composer, with feature detection methods (`supportsDefer()`, `supportsIslands()`, `supportsAsyncActions()`).

Concerns and Traits

- `RedirectsToPanelRoute`: Helper for redirecting to panel routes from components.

Exceptions

- `PanelException`: Generic panel errors.
- `PanelNotFoundException`: Panel or theme not found in config.
- `PanelStyleNotFoundException`: Style customization file missing.

Public Pages

- Public layout (`panel::layouts.public`) for unauthenticated pages outside the panel.
- Page registration via config `public_pages` array or module `publicRoutes()` method.
- `panel:make-page` command for generating public Livewire pages.

Testing

- `PanelTestCase`: Base test class extending Orchestra Testbench with panel config setup.
- `PanelTestHelpers`: Helper methods for test setup and assertions.
- SQLite in-memory test database with User model.
- Compatible with Pest 2/3 and PHPUnit.
- Livewire widget integration tests with `Livewire::withoutLazyLoading()->test()`.
- Performance benchmark tests with time-budget assertions.
- Stress tests with memory profiling for all Registry classes (100-500 items).
- Unit tests for ThemeRegistry, ModuleRegistry, WidgetRegistry, PluginRegistry, NavigationRegistry, SearchRegistry.
- Artisan command tests for all 7 scaffolding commands (make-widget, make-theme, make-plugin, make-module, make-page, make-component, make-style).
- Code coverage CI job with PCOV and minimum 60% threshold.
- Mutation testing CI job with Infection PHP (60% MSI, 70% covered MSI).
- `panel:make-widget --test` flag for auto-generating widget test files.
- Rector config for automated PHP upgrade refactoring.

Compatibility

- PHP 8.1, 8.2, 8.3, 8.4, 8.5
- Laravel 10, 11, 12, 13
- Livewire 3.x, 4.x

Documentation

- Full documentation: installation, configuration, navigation, modules, widgets, themes, customization, commands, icons, components, localization, plugins, events, API reference.
