<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use AlpDevelop\LivewirePanel\Modules\Auth\Notifications\PanelForgotPasswordNotification;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

abstract class AbstractForgotPasswordComponent extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    public bool $sent = false;

    #[Locked]
    public string $panelId = '';

    public function mount(): void
    {
        $resolver      = app(PanelResolver::class);
        $this->panelId = $resolver->resolveFromRequest(request());
    }

    public function submit(): void
    {
        $this->validate();

        $throttleKey = 'panel-forgot-password:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('auth.throttle', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $broker = Password::broker();
        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $user = $broker->getUser(['email' => $this->email]);

        if ($user !== null) {
            /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
            $token = $broker->createToken($user);

            $resolver    = app(PanelResolver::class);
            $panelConfig = $resolver->resolveById($this->panelId);
            $expire      = (int) config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

            $resetUrl = route("panel.{$this->panelId}.auth.reset-password", [
                'token' => $token,
                'email' => $this->email,
            ]);

            $notificationClass = $panelConfig['components']['forgot-password-notification'] ?? null;

            if ($notificationClass !== null && class_exists($notificationClass)) {
                $user->notify(new $notificationClass($token, $resetUrl, $expire));
            } else {
                $user->notify(new PanelForgotPasswordNotification($token, $resetUrl, $expire));
            }
        }

        $this->sent = true;
    }

    abstract public function render(): View;
}
