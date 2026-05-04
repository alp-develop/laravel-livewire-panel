# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [1.0.5] - 2026-05-04

### Fixed

- Poll requests now pause completely when the browser tab is in the background, preventing 504 Gateway Timeout errors caused by server cold-start under idle conditions. Uses the browser `visibilitychange` API to signal Livewire's internal offline state (compatible with Livewire 3 and 4).
- If a poll request returns a 5xx error (e.g. 504), Livewire's error modal is suppressed and the page reloads cleanly instead of showing the nginx error page.


## [1.0.4] - 2026-05-04

### Added

- `layout.title` in style config — global `<title>` tag applied to all layouts (`app`, `auth`, `auth-base`, `blank`, `public`). A page-level `$title` set via `#[Layout]` always takes priority. `null` falls back to each layout's built-in default.

### Fixed

- Navigation groups (`children`) now render at full sidebar width and inherit theme CSS variables.
- Navigation groups auto-expand when their active child is the current route.
- Chevron icon hidden on collapsed sidebar; visible when expanded or hover-expanded.
- Sidebar hover-expand state preserved across SPA navigation: `panel-sidebar--hover-open` is captured before the DOM swap and restored after if the mouse is still over the sidebar.
- Navigation search results (`NavigationSearchProvider`) now apply translations to `label` and `description` via `__()`, matching sidebar behavior.

### Refactored

- PHP logic moved out of all Livewire blades (`sidebar`, `navbar`, `search`, `notifications`) into component `render()` methods. All blades are now purely presentational.
- Auth views (`login`, `register`, `forgot-password`, `reset-password`) no longer call `Route::has()` — booleans pre-computed in each component's `render()`.
- Layout blades (`auth`, `auth-base`, `blank`) no longer contain `@php` blocks. `PanelRenderer::layoutConfig()` now exposes `locale_enabled`, `locale_show_on_auth`, and pre-computes `dark_mode_show_on_auth`.

## [1.0.3] - 2026-05-02

### Added

- `PanelManager` — central access point to all registries (themes, modules, plugins, widgets, search, notifications) via the service container. Exposes `currentId()`, `for()`, `route()`, `kernel()`, `clearCaches()`.
- `Panel` Facade — Laravel auto-discovered facade (`Panel::`) with full `@method` PHPDoc for IDE autocompletion. Registered in `composer.json` extra.laravel.aliases.
- `PanelManager::clearCaches()` — flushes `SearchRegistry` cache and `PanelRenderer` static cache in a single call.
- `PanelRateLimitMiddleware` — configurable rate limiting per panel and per action (login, register, search). Registered as `panel.throttle` middleware alias.
- `rate_limiting` config section per panel with defaults: `login` (5 attempts/min), `register` (3 attempts/min), `search` (30 attempts/min).
- `SearchRegistry` cache memoization — results are memoized by `panelId:query` key to avoid redundant provider calls on repeated identical queries.
- `SearchRegistryInterface::clearCache()` — added to the interface contract and implemented in `SearchRegistry`.
- `PanelRenderer::clearCache()` — static method to flush the memoized render cache.
- `PanelKernel::widgets()` — exposes the `WidgetRegistry` instance.
- `NavigationRegistry::buildRouteMap()` — O(1) route-to-title map, memoized per panel, invalidated on `add()`/`addGroup()` calls.
- `docs/facade.md` — full documentation for the Panel Facade with method table, usage examples, and global helpers reference.
- `docs/api-reference.md` — added `SearchRegistryInterface`, `NotificationRegistryInterface` tables; added `darkCssVariables()` to `ThemeInterface` table; corrected return types for `PluginInterface::registerNavigation()` and `registerWidgets()`.
- Publishable stubs via `vendor:publish --tag=panel-stubs` for customizing code generation templates (widget, module, page, plugin, theme, notification).
- Tests: `SearchRegistry` cache memoization and `clearCache()` re-evaluation.
- Tests: `PanelManager` and `Panel` Facade resolution from container.
- Tests: `PanelRateLimitMiddleware` allows within limit and blocks after max attempts.
- Tests: `NavigationRegistry::buildRouteMap()` for direct items, groups, memoization, empty routes, and unknown panels.
- Tests: `PanelPortalBuilder::route()`, `home()`, parameters, and `to_panel()` helper.
- Tests: `panel_component()` helper resolution, default component, empty key, and config override via `PanelHelpersWithOverrideTest`.
- Tests: `panel:make-notification` and `panel:list` artisan commands.
- CI: coverage job threshold raised from 55% to 75%.



- **404 Not Found page**: Built-in wildcard route per panel renders a branded 404 component when no other route matches. Supports full customization via `components.not-found` config key. Run `php artisan panel:make-component not-found --panel=admin` to generate a custom component.
- `AbstractNotFoundComponent` — base class for custom 404 components. Exposes `$panelId` property (`#[Locked]`) populated in `mount()`.
- `panel:make-component not-found` — new type for the make-component command. Generates `App\Livewire\{Panel}NotFound` class and corresponding Blade view.
- Translation keys `not_found_title`, `not_found_message`, `back_to_home` added to all 10 supported languages.
- `sidebar.hover_expand` config option (default `false`). When `true` and the sidebar is collapsed, hovering over it expands it to full width floating over the content (desktop only, no effect on mobile).
- CDN SRI (Subresource Integrity) support: each CDN entry in config now accepts the array format `['url' => '...', 'integrity' => 'sha384-...']` alongside the plain string format. When `integrity` is set, the rendered `<link>` and `<script>` tags include `integrity` and `crossorigin="anonymous"` attributes.
- `NotificationProviderInterface::markAsRead()` now accepts an optional `int|null $userId` parameter for ownership validation. Existing implementations are backward compatible.
- Performance: ETag + HTTP 304 caching on asset serving via `AssetController`
- Performance: Instance memoization in `PanelGate` for driver instances per panel
- Performance: `cssAssets()` and `jsAssets()` memoized per `panelId|layout|path` in `PanelRenderer`
- Performance: `buildRouteMap()` in `NavigationRegistry` for O(1) route-to-title lookups
- Performance: Identical query guard and result caching in `PanelSearch`
- Performance: Instance cache for notification count and items in `AbstractPanelNotifications`
- Performance: Memoization in `CdnPluginResolver` keyed by CDN config hash and current path
- Performance: `NavigationSearchProvider` limits results to 15 items
- Security: Octane-safe `scoped()` bindings for `PanelContext` and `PanelGate`
- Security: `LocaleController` validates locale against panel whitelist before setting session
- `CdnManagerInterface` — formal interface for `CdnManager` with typed signatures
- PHPDoc on `AbstractModule`, `AbstractWidget`, and `AbstractTheme`
- Tests: `PanelForgotPasswordTest` covering submit, notification dispatch, rate limiting, and validation
- Tests: `PanelResetPasswordTest` covering invalid token redirect, token population, validation, and rate limiting
- Tests: `DashboardPageTest` covering render with and without configured stats

### Changed

- `PanelGate` now receives `PanelContext` via constructor injection
- `CdnManager` resolves `CdnPluginResolver` from container to reuse memoized instance
- `AbstractLoginComponent` and `AbstractRegisterComponent` use `$this->panelId` instead of re-resolving from request

### Fixed

- **ForgotPassword / ResetPassword**: Broker is now resolved from the panel's configured guard provider instead of the default broker, fixing silent email delivery failures when using non-default guards. `$expire` also uses the resolved broker.
- Page transition fade: `panel-page-entering` class could remain permanently on `.panel-content` if `livewire:navigated` never fired. Now uses double `requestAnimationFrame` inside `onSwap`.
- Sidebar hover-expand background was transparent on the overflow area. Fixed with `position: absolute` on `.panel-sidebar-inner` scoped to `@media (min-width: 769px)`.
- PHPStan level 8: resolved `missingType.iterableValue` in `CdnPluginResolver::normalizeEntry()` and added typed `@return` to `CdnManager` and `CdnPluginResolver::resolveAssets()`.

### Security

- **Password exposure**: Login, Register, and Reset Password components now clear the plaintext password from Livewire component state immediately after use, preventing it from appearing in the `livewire/update` JSON payload.
- **Register type check**: Replaced `assert()` with an explicit `instanceof` check. `assert()` is a no-op when `zend.assertions=-1` (standard production config), so the previous guard never ran in production.
- **InstallCommand**: Regex validation on panel ID prompt prevents PHP code injection in the generated config.
- **Login brute-force**: Rate limiter key includes `sha1(email) + IP` to prevent credential stuffing from rotating IPs.
- **PanelSearch**: `updatedQuery()` truncates search input to 100 characters before processing.
- **ForgotPassword timing**: Random `usleep` (80–150 ms) on unknown email to prevent user enumeration via timing side-channel.
- **Notifications IDOR**: `markAsRead()` passes the authenticated user's ID for ownership validation.

## [1.0.2] - 2026-04-12

### Added

- `AbstractPanelNotifications`: Base class for custom notification components. Provides `mount()`, `render()`, `markAsRead()`, `markAllAsRead()` and `view()` override point.
- `notifications` type for `panel:make-component` command. Generates a Livewire component extending `AbstractPanelNotifications` and a full Blade view copy.
- `components.notifications` config key to override the default notification component per panel.
- `docs/notifications.md`: Dedicated documentation for the notification system.
- Cross-references to `docs/notifications.md` from `README.md`, `docs/api-reference.md`, and `docs/customization.md`.

### Changed

- `PanelNotifications` now extends `AbstractPanelNotifications` instead of `Component` directly.
- Navbar resolves the notification component via `panel_component('notifications')` instead of a hardcoded Livewire name.

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
