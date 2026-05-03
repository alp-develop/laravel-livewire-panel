<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeComponentCommand extends Command
{
    protected $signature = 'panel:make-component
                            {type : Component type: login, register, forgot-password, reset-password, forgot-password-notification, not-found, sidebar, navbar, notifications, user-popover-header}
                            {--panel= : Panel ID (e.g.: admin). Used as class suffix}';

    protected $description = 'Generate a custom Livewire component ready to modify';

    private const TYPES = ['login', 'register', 'forgot-password', 'reset-password', 'forgot-password-notification', 'not-found', 'sidebar', 'navbar', 'notifications', 'user-popover-header'];

    public function handle(): int
    {
        $raw     = $this->argument('type');
        $type    = strtolower(is_string($raw) ? $raw : '');
        $rawPanel = $this->option('panel');
        $panelId = is_string($rawPanel) ? $rawPanel : 'panel';

        if (!in_array($type, self::TYPES, true)) {
            $this->error('Invalid type. Use: ' . implode(', ', self::TYPES));
            return Command::FAILURE;
        }

        if ($type === 'forgot-password-notification') {
            return $this->publishForgotPasswordNotification();
        }

        if ($type === 'notifications') {
            return $this->publishNotifications();
        }

        if ($type === 'user-popover-header') {
            return $this->publishUserPopoverHeader();
        }

        $panelStudly  = Str::studly($panelId);
        $typeStudly   = Str::studly($type);
        $className    = "{$panelStudly}{$typeStudly}";
        $viewName     = Str::kebab($type);
        $panelKebab   = Str::kebab($panelId);

        [$classPath, $viewPath, $namespace, $viewKey] = match ($type) {
            'login', 'register', 'forgot-password', 'reset-password' => [
                app_path("Livewire/Auth/{$className}.php"),
                resource_path("views/livewire/auth/{$panelKebab}-{$viewName}.blade.php"),
                'App\\Livewire\\Auth',
                "livewire.auth.{$panelKebab}-{$viewName}",
            ],
            'not-found' => [
                app_path("Livewire/{$className}.php"),
                resource_path("views/livewire/{$panelKebab}-{$viewName}.blade.php"),
                'App\\Livewire',
                "livewire.{$panelKebab}-{$viewName}",
            ],
            'sidebar', 'navbar' => [
                app_path("Livewire/{$className}.php"),
                resource_path("views/livewire/{$panelKebab}-{$viewName}.blade.php"),
                'App\\Livewire',
                "livewire.{$panelKebab}-{$viewName}",
            ],
            default => throw new \UnexpectedValueException("Unhandled component type: {$type}"),
        };

        if (file_exists($classPath)) {
            $this->error("Component {$className} already exists in {$classPath}");
            return Command::FAILURE;
        }

        $classContent  = $this->buildClass($type, $namespace, $className, $viewKey);
        $viewContent   = $this->buildView($type);

        $this->ensureDirectory(dirname($classPath));
        $this->ensureDirectory(dirname($viewPath));

        file_put_contents($classPath, $classContent);
        file_put_contents($viewPath, $viewContent);

        $this->info("Component {$type} generated:");
        $this->line("  PHP : {$classPath}");
        $this->line("  View: {$viewPath}");
        $this->newLine();
        $this->line("Add this to your config/laravel-livewire-panel.php in panel <comment>{$panelId}</comment>:");
        $this->line("  'components' => [");
        $this->line("      '{$type}' => \\{$namespace}\\{$className}::class,");
        $this->line("  ],");

        return Command::SUCCESS;
    }

    private function buildClass(string $type, string $namespace, string $className, string $viewKey): string
    {
        $abstract = match ($type) {
            'login'           => 'AbstractLoginComponent',
            'register'        => 'AbstractRegisterComponent',
            'forgot-password' => 'AbstractForgotPasswordComponent',
            'reset-password'  => 'AbstractResetPasswordComponent',
            'not-found'       => 'AbstractNotFoundComponent',
            'sidebar'         => 'AbstractSidebar',
            'navbar'          => 'AbstractNavbar',
            default           => throw new \UnexpectedValueException("Unhandled type: {$type}"),
        };

        $use = match ($type) {
            'login', 'register', 'forgot-password', 'reset-password' => "use AlpDevelop\\LivewirePanel\\Modules\\Auth\\Http\\Livewire\\{$abstract};",
            'not-found' => "use AlpDevelop\\LivewirePanel\\Modules\\Errors\\Http\\Livewire\\{$abstract};",
            'sidebar', 'navbar' => "use AlpDevelop\\LivewirePanel\\View\\Livewire\\{$abstract};",
            default             => throw new \UnexpectedValueException("Unhandled type: {$type}"),
        };

        $layoutAttr = match ($type) {
            'login'           => "\n#[Layout('panel::layouts.auth-base', ['title' => 'Sign in'])]",
            'register'        => "\n#[Layout('panel::layouts.auth-base', ['title' => 'Create account'])]",
            'forgot-password' => "\n#[Layout('panel::layouts.auth-base', ['title' => 'Forgot password'])]",
            'reset-password'  => "\n#[Layout('panel::layouts.auth-base', ['title' => 'Reset password'])]",
            'not-found'       => "\n#[Layout('panel::layouts.app', ['title' => '404 Not Found'])]",
            default           => '',
        };

        $layoutUse = in_array($type, ['login', 'register', 'forgot-password', 'reset-password', 'not-found'], true)
            ? "\nuse Livewire\\Attributes\\Layout;"
            : '';

        $renderBody = match ($type) {
            'sidebar' => "    protected function view(): string\n    {\n        return '{$viewKey}';\n    }",
            'navbar'  => "    protected function view(): string\n    {\n        return '{$viewKey}';\n    }",
            default   => "    public function render(): \\Illuminate\\Contracts\\View\\View\n    {\n        return view('{$viewKey}');\n    }",
        };

        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace {$namespace};

        {$use}{$layoutUse}
        use Illuminate\Contracts\View\View;
        {$layoutAttr}
        final class {$className} extends {$abstract}
        {
        {$renderBody}
        }
        PHP;
    }

    private function buildView(string $type): string
    {
        return match ($type) {
            'login'           => $this->loginViewStub(),
            'register'        => $this->registerViewStub(),
            'forgot-password' => $this->forgotPasswordViewStub(),
            'reset-password'  => $this->resetPasswordViewStub(),
            'not-found'       => $this->notFoundViewStub(),
            'sidebar'  => "@extends('panel::livewire.sidebar')\n\n{{-- Modify this view to customize the sidebar --}}\n",
            'navbar'   => "@extends('panel::livewire.navbar')\n\n{{-- Modify this view to customize the navbar --}}\n",
            default    => '',
        };
    }

    private function loginViewStub(): string
    {
        return <<<'BLADE'
        <div class="auth-container">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <x-panel::icon name="layer-group" size="28" />
                </div>
                <div class="auth-brand-name">Panel Admin</div>
            </div>

            <div class="auth-card">
                <h2 class="auth-title">{{ __('panel::messages.sign_in') }}</h2>
                <p class="auth-subtitle">{{ __('panel::messages.sign_in_subtitle') }}</p>

                @if (session('error'))
                    <x-panel::alert variant="danger" style="margin-bottom:1rem">
                        {{ session('error') }}
                    </x-panel::alert>
                @endif

                @if (session('status'))
                    <x-panel::alert variant="success" style="margin-bottom:1rem">
                        {{ session('status') }}
                    </x-panel::alert>
                @endif

                <form wire:submit="login">
                    <div class="auth-field">
                        <label class="auth-label" for="email">{{ __('panel::messages.email') }}</label>
                        <input
                            id="email"
                            type="email"
                            class="auth-input {{ $errors->has('email') ? 'invalid' : '' }}"
                            wire:model="email"
                            autocomplete="email"
                            autofocus
                        />
                        @error('email') <div class="auth-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password">{{ __('panel::messages.password') }}</label>
                        <input
                            id="password"
                            type="password"
                            class="auth-input {{ $errors->has('password') ? 'invalid' : '' }}"
                            wire:model="password"
                            autocomplete="current-password"
                        />
                        @error('password') <div class="auth-error">{{ $message }}</div> @enderror
                    </div>

                    <label class="auth-check">
                        <input type="checkbox" wire:model="remember" />
                        <span class="auth-check-label">{{ __('panel::messages.remember_me') }}</span>
                    </label>

                    <x-panel::button type="submit" variant="primary" size="md" style="width:100%">
                        {{ __('panel::messages.sign_in') }}
                    </x-panel::button>
                </form>

                <div class="auth-footer">
                    @if (Route::has("panel.{$panelId}.auth.forgot-password"))
                        <a href="{{ route("panel.{$panelId}.auth.forgot-password") }}">{{ __('panel::messages.forgot_password') }}</a>
                    @endif
                </div>
            </div>
        </div>
        BLADE;
    }

    private function registerViewStub(): string
    {
        return <<<'BLADE'
        <div class="auth-container">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <x-panel::icon name="layer-group" size="28" />
                </div>
                <div class="auth-brand-name">Panel Admin</div>
            </div>

            <div class="auth-card">
                <h2 class="auth-title">{{ __('panel::messages.create_account') }}</h2>
                <p class="auth-subtitle">{{ __('panel::messages.register_subtitle') }}</p>

                <form wire:submit="register">
                    <div class="auth-field">
                        <label class="auth-label" for="name">{{ __('panel::messages.full_name') }}</label>
                        <input
                            id="name"
                            type="text"
                            class="auth-input {{ $errors->has('name') ? 'invalid' : '' }}"
                            wire:model="name"
                            autocomplete="name"
                            autofocus
                        />
                        @error('name') <div class="auth-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="email">{{ __('panel::messages.email') }}</label>
                        <input
                            id="email"
                            type="email"
                            class="auth-input {{ $errors->has('email') ? 'invalid' : '' }}"
                            wire:model="email"
                            autocomplete="email"
                        />
                        @error('email') <div class="auth-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password">{{ __('panel::messages.password') }}</label>
                        <input
                            id="password"
                            type="password"
                            class="auth-input {{ $errors->has('password') ? 'invalid' : '' }}"
                            wire:model="password"
                            autocomplete="new-password"
                        />
                        @error('password') <div class="auth-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password_confirmation">{{ __('panel::messages.confirm_password') }}</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            class="auth-input"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                        />
                    </div>

                    <x-panel::button type="submit" variant="primary" size="md" style="width:100%">
                        {{ __('panel::messages.create_account') }}
                    </x-panel::button>
                </form>

                <div class="auth-footer">
                    @if (Route::has("panel.{$panelId}.auth.login"))
                        <a href="{{ route("panel.{$panelId}.auth.login") }}">{{ __('panel::messages.already_have_account') }}</a>
                    @endif
                </div>
            </div>
        </div>
        BLADE;
    }

    private function forgotPasswordViewStub(): string
    {
        return <<<'BLADE'
        <div class="auth-container">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <x-panel::icon name="layer-group" size="28" />
                </div>
                <div class="auth-brand-name">Panel Admin</div>
            </div>

            <div class="auth-card">
                <h2 class="auth-title">{{ __('panel::messages.forgot_password_title') }}</h2>
                <p class="auth-subtitle">{{ __('panel::messages.forgot_password_subtitle') }}</p>

                @if ($sent)
                    <x-panel::alert variant="success">
                        <x-panel::icon name="check" size="16" style="flex-shrink:0;margin-right:6px" />
                        {{ __('panel::messages.recovery_link_sent') }}
                    </x-panel::alert>
                @else
                    <form wire:submit="submit">
                        <div class="auth-field">
                            <label class="auth-label" for="email">{{ __('panel::messages.email') }}</label>
                            <input
                                id="email"
                                type="email"
                                class="auth-input {{ $errors->has('email') ? 'invalid' : '' }}"
                                wire:model="email"
                                autocomplete="email"
                                autofocus
                            />
                            @error('email') <div class="auth-error">{{ $message }}</div> @enderror
                        </div>

                        <x-panel::button type="submit" variant="primary" size="md" style="width:100%">
                            {{ __('panel::messages.send_link') }}
                        </x-panel::button>
                    </form>
                @endif

                <div class="auth-footer">
                    @if (Route::has("panel.{$panelId}.auth.login"))
                        <a href="{{ route("panel.{$panelId}.auth.login") }}">
                            <x-panel::icon name="arrow-left" size="14" style="vertical-align:-2px;margin-right:2px" />
                            {{ __('panel::messages.back_to_sign_in') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        BLADE;
    }

    private function resetPasswordViewStub(): string
    {
        return <<<'BLADE'
        <div class="auth-container">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <x-panel::icon name="layer-group" size="28" />
                </div>
                <div class="auth-brand-name">Panel Admin</div>
            </div>

            <div class="auth-card">
                <h2 class="auth-title">{{ __('panel::messages.reset_password_title') }}</h2>
                <p class="auth-subtitle">{{ __('panel::messages.reset_password_subtitle') }}</p>

                <form wire:submit="submit">
                    <div class="auth-field">
                        <label class="auth-label" for="email">{{ __('panel::messages.email') }}</label>
                        <input
                            id="email"
                            type="email"
                            class="auth-input"
                            wire:model="email"
                            readonly
                        />
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password">{{ __('panel::messages.new_password') }}</label>
                        <input
                            id="password"
                            type="password"
                            class="auth-input {{ $errors->has('password') ? 'invalid' : '' }}"
                            wire:model="password"
                            autocomplete="new-password"
                            autofocus
                        />
                        @error('password') <div class="auth-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="auth-field">
                        <label class="auth-label" for="password_confirmation">{{ __('panel::messages.confirm_password') }}</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            class="auth-input"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                        />
                    </div>

                    <x-panel::button type="submit" variant="primary" size="md" style="width:100%">
                        {{ __('panel::messages.reset_password_title') }}
                    </x-panel::button>
                </form>

                <div class="auth-footer">
                    @if (Route::has("panel.{$panelId}.auth.login"))
                        <a href="{{ route("panel.{$panelId}.auth.login") }}">
                            <x-panel::icon name="arrow-left" size="14" style="vertical-align:-2px;margin-right:2px" />
                            {{ __('panel::messages.back_to_sign_in') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        BLADE;
    }

    private function publishNotifications(): int
    {
        $panel       = is_string($this->option('panel')) ? $this->option('panel') : 'admin';
        $panelStudly = str($panel)->studly()->toString();
        $panelKebab  = str($panel)->kebab()->toString();

        $className = "{$panelStudly}Notifications";
        $namespace = 'App\\Livewire';
        $classPath = app_path("Livewire/{$className}.php");
        $viewName  = "{$panelKebab}-notifications";
        $viewPath  = resource_path("views/livewire/{$viewName}.blade.php");

        if (file_exists($classPath)) {
            $this->error("Already exists: {$classPath}");
            return Command::FAILURE;
        }

        $this->ensureDirectory(dirname($classPath));
        $this->ensureDirectory(dirname($viewPath));

        file_put_contents($classPath, $this->notificationsClassStub($namespace, $className, $viewName));
        file_put_contents($viewPath, $this->notificationsViewStub());

        $this->info("Component generated:");
        $this->line("  PHP : {$classPath}");
        $this->line("  View: {$viewPath}");
        $this->newLine();
        $this->line("Add to your panel config under 'components':");
        $this->line("  'notifications' => \\{$namespace}\\{$className}::class,");

        return Command::SUCCESS;
    }

    private function notificationsClassStub(string $namespace, string $className, string $viewName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use AlpDevelop\LivewirePanel\View\Livewire\AbstractPanelNotifications;

final class {$className} extends AbstractPanelNotifications
{
    protected function view(): string
    {
        return 'livewire.{$viewName}';
    }
}
PHP;
    }

    private function notificationsViewStub(): string
    {
        return <<<'BLADE'
<div @if ($polling) wire:poll.{{ $pollingInterval }}s @endif x-data="{ open: false }" x-on:click.outside="open = false" class="panel-dropdown">
    <button class="panel-navbar-icon-btn" x-on:click="open = !open" title="{{ __('panel::messages.notifications') }}" style="position:relative">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
        @if ($count > 0)
            <span class="panel-notification-badge">{{ $count > 99 ? '99+' : $count }}</span>
        @endif
    </button>

    <div class="panel-dropdown-menu panel-dropdown-menu--right panel-notification-dropdown" x-show="open" x-cloak x-transition>
        <div class="panel-notification-header">
            <span>{{ __('panel::messages.notifications') }}</span>
            @if ($count > 0)
                <button type="button" wire:click.prevent="markAllAsRead" class="panel-notification-mark-all" x-on:click.prevent.stop>
                    {{ __('panel::messages.mark_all_as_read') }}
                </button>
            @endif
        </div>

        @if (count($items) > 0)
            <div class="panel-notification-list">
                @foreach ($items as $item)
                    <div wire:key="notif-{{ $item['id'] }}" class="panel-notification-item {{ empty($item['read']) ? 'panel-notification-item--unread' : '' }}">
                        <div style="display:flex;align-items:flex-start;padding:0.7rem 1rem;gap:0.65rem">
                            @if (!empty($item['route']))
                                <a href="{{ $item['route'] }}" wire:navigate x-on:click="open = false" style="display:flex;align-items:flex-start;gap:0.65rem;flex:1;min-width:0;text-decoration:none;color:inherit">
                            @else
                                <div style="display:flex;align-items:flex-start;gap:0.65rem;flex:1;min-width:0">
                            @endif
                                @php $safeColor = (!empty($item['color']) && preg_match('/^#[0-9a-fA-F]{6}$/', $item['color'])) ? $item['color'] : ''; @endphp
                                <div class="panel-notification-icon-wrap" style="{{ $safeColor !== '' ? 'background:' . $safeColor . '20;color:' . $safeColor : '' }}">
                                    <x-panel::icon :name="$item['icon'] ?? 'bell'" size="16" />
                                </div>
                                <div class="panel-notification-body">
                                    <div class="panel-notification-title">{{ $item['title'] }}</div>
                                    @if (!empty($item['body']))
                                        <div class="panel-notification-text">{{ $item['body'] }}</div>
                                    @endif
                                    @if (!empty($item['time']))
                                        <div class="panel-notification-time">{{ $item['time'] }}</div>
                                    @endif
                                </div>
                            @if (!empty($item['route']))
                                </a>
                            @else
                                </div>
                            @endif
                            @if (empty($item['read']))
                                <button type="button" wire:click.prevent="markAsRead({{ \Illuminate\Support\Js::from($item['id']) }})" class="panel-notification-dismiss" title="{{ __('panel::messages.mark_as_read') }}" style="align-self:center;flex-shrink:0">
                                    <x-panel::icon name="check" size="14" />
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="panel-dropdown-empty">{{ __('panel::messages.no_notifications') }}</div>
        @endif
    </div>
</div>
BLADE;
    }

    private function publishUserPopoverHeader(): int
    {
        $panel       = is_string($this->option('panel')) ? $this->option('panel') : 'admin';
        $panelStudly = str($panel)->studly()->toString();
        $panelKebab  = str($panel)->kebab()->toString();

        $className = "{$panelStudly}UserPopoverHeader";
        $namespace = 'App\\Livewire';
        $classPath = app_path("Livewire/{$className}.php");
        $viewName  = "{$panelKebab}-user-popover-header";
        $viewPath  = resource_path("views/livewire/{$viewName}.blade.php");

        if (file_exists($classPath)) {
            $this->error("Already exists: {$classPath}");
            return Command::FAILURE;
        }

        $this->ensureDirectory(dirname($classPath));
        $this->ensureDirectory(dirname($viewPath));

        file_put_contents($classPath, $this->userPopoverHeaderClassStub($namespace, $className, $viewName));
        file_put_contents($viewPath, $this->userPopoverHeaderViewStub());

        $this->info("Component generated:");
        $this->line("  PHP : {$classPath}");
        $this->line("  View: {$viewPath}");
        $this->newLine();
        $this->line("Add to your style config under 'navbar':");
        $this->line("  'user_popover_header_component' => '{$panelKebab}-user-popover-header',");

        return Command::SUCCESS;
    }

    private function userPopoverHeaderClassStub(string $namespace, string $className, string $viewName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use Livewire\Attributes\Locked;
use Livewire\Component;
use Illuminate\Contracts\View\View;

final class {$className} extends Component
{
    #[Locked]
    public \$user;

    public bool \$showAvatar = true;
    public ?string \$avatarUrl = null;

    public function render(): View
    {
        return view('livewire.{$viewName}');
    }
}
PHP;
    }

    private function userPopoverHeaderViewStub(): string
    {
        return <<<'BLADE'
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
BLADE;
    }

    private function publishForgotPasswordNotification(): int
    {
        $panel  = is_string($this->option('panel')) ? $this->option('panel') : 'admin';
        $suffix = str($panel)->studly()->toString();

        $classDir  = app_path('Notifications');
        $className = "{$suffix}ForgotPasswordNotification";
        $classPath = "{$classDir}/{$className}.php";

        $viewDir  = resource_path('views/emails');
        $viewName = str($panel)->kebab()->toString() . '-reset-password';
        $viewPath = "{$viewDir}/{$viewName}.blade.php";

        if (file_exists($classPath)) {
            $this->error("Already exists: {$classPath}");
            return Command::FAILURE;
        }

        $this->ensureDirectory($classDir);
        $this->ensureDirectory($viewDir);

        $classContent = $this->forgotPasswordNotificationStub($className, "emails.{$viewName}");
        file_put_contents($classPath, $classContent);

        $source = dirname(__DIR__, 2) . '/resources/views/auth/emails/reset-password.blade.php';
        copy($source, $viewPath);

        $this->info("Created: {$classPath}");
        $this->info("Created: {$viewPath}");
        $this->newLine();
        $this->line("Add to your panel config:");
        $this->line("  'components' => ['forgot-password-notification' => \\App\\Notifications\\{$className}::class]");

        return Command::SUCCESS;
    }

    private function forgotPasswordNotificationStub(string $className, string $viewKey): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Notifications;

        use AlpDevelop\LivewirePanel\Modules\Auth\Notifications\PanelForgotPasswordNotification;

        class {$className} extends PanelForgotPasswordNotification
        {
            protected function emailSubject(): string
            {
                return __('panel::messages.reset_password_title');
            }

            protected function emailView(): string
            {
                return '{$viewKey}';
            }

            protected function emailData(mixed \$notifiable): array
            {
                return array_merge(parent::emailData(\$notifiable), [
                    //
                ]);
            }
        }
        PHP;
    }

    private function ensureDirectory(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function notFoundViewStub(): string
    {
        return <<<'BLADE'
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:4rem 2rem;text-align:center;min-height:40vh">
    <div style="font-size:6rem;font-weight:800;line-height:1;margin-bottom:1rem;color:var(--panel-primary,#4f46e5)">404</div>
    <h1 style="margin:0 0 .75rem;font-size:1.5rem;font-weight:700;color:var(--panel-text-primary,#1e293b)">{{ __('panel::messages.not_found_title') }}</h1>
    <p style="margin:0 0 2rem;font-size:.95rem;color:var(--panel-text-muted,#64748b);max-width:420px;line-height:1.6">{{ __('panel::messages.not_found_message') }}</p>
    <a href="{{ panel_route($panelId, 'home') }}" style="display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.25rem;background:var(--panel-primary,#4f46e5);color:#fff;font-size:.875rem;font-weight:500;border-radius:6px;text-decoration:none">
        <x-panel::icon name="arrow-left" size="14" />
        {{ __('panel::messages.back_to_home') }}
    </a>
</div>
BLADE;
    }
}
