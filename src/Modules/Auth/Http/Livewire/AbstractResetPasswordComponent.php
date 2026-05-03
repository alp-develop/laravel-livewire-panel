<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

abstract class AbstractResetPasswordComponent extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    #[Locked]
    public string $token = '';

    #[Locked]
    public string $panelId = '';

    public function mount(string $token): void
    {
        $resolver      = app(PanelResolver::class);
        $this->panelId = $resolver->resolveFromRequest(request());
        $this->token   = $token;
        $this->email   = (string) request()->query('email', '');

        $panelConfig = $resolver->resolveById($this->panelId);
        $guard       = (string) ($panelConfig['guard'] ?? 'web');
        $provider    = (string) config("auth.guards.{$guard}.provider", 'users');
        $brokerName  = null;

        foreach ((array) config('auth.passwords', []) as $name => $cfg) {
            if (($cfg['provider'] ?? null) === $provider) {
                $brokerName = (string) $name;
                break;
            }
        }

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker($brokerName);
        $user   = $broker->getUser(['email' => $this->email]);

        if ($user === null || !$broker->tokenExists($user, $this->token)) {
            session()->flash('status', __('passwords.token'));
            $this->redirect(route("panel.{$this->panelId}.auth.login"));
        }
    }

    public function submit(): void
    {
        $this->validate();

        $throttleKey = 'panel-reset-password:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('auth.throttle', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $resolver    = app(PanelResolver::class);
        $panelConfig = $resolver->resolveById($this->panelId);
        $guard       = (string) ($panelConfig['guard'] ?? 'web');
        $provider    = (string) config("auth.guards.{$guard}.provider", 'users');
        $brokerName  = null;

        foreach ((array) config('auth.passwords', []) as $name => $cfg) {
            if (($cfg['provider'] ?? null) === $provider) {
                $brokerName = (string) $name;
                break;
            }
        }

        $status = Password::broker($brokerName)->reset(
            [
                'email'                 => $this->email,
                'password'              => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token'                 => $this->token,
            ],
            function ($user, string $password): void {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        $this->password             = '';
        $this->password_confirmation = '';

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __('panel::messages.password_reset_success'));
            $this->redirect(route("panel.{$this->panelId}.auth.login"));

            return;
        }

        $this->addError('email', __($status));
    }

    abstract public function render(): View;
}
