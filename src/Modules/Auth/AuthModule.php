<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth;

use AlpDevelop\LivewirePanel\Compat\LivewireCompat;
use AlpDevelop\LivewirePanel\Events\UserLoggedOut;
use AlpDevelop\LivewirePanel\Modules\AbstractModule;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ForgotPasswordComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\LoginComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\RegisterComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ResetPasswordComponent;
use Illuminate\Support\Facades\Route;

final class AuthModule extends AbstractModule
{
    public function id(): string
    {
        return 'auth';
    }

    public function routes(): void
    {
        $panelId              = $this->panelId();
        $prefix               = $this->prefix();
        $guard                = $this->guard();
        $registrationEnabled  = (bool) ($this->panelConfig['registration_enabled'] ?? false);
        $components           = $this->panelConfig['components'] ?? [];

        $loginClass           = $components['login']           ?? LoginComponent::class;
        $registerClass        = $components['register']        ?? RegisterComponent::class;
        $forgotPasswordClass  = $components['forgot-password'] ?? ForgotPasswordComponent::class;
        $resetPasswordClass   = $components['reset-password']  ?? ResetPasswordComponent::class;

        Route::middleware(['web'])
            ->prefix($prefix)
            ->name("panel.{$panelId}.")
            ->group(function () use ($panelId, $guard, $registrationEnabled, $loginClass, $registerClass, $forgotPasswordClass, $resetPasswordClass) {
                LivewireCompat::pageRoute('/login', $loginClass)->name('auth.login');

                LivewireCompat::pageRoute('/forgot-password', $forgotPasswordClass)
                    ->name('auth.forgot-password');

                LivewireCompat::pageRoute('/reset-password/{token}', $resetPasswordClass)
                    ->name('auth.reset-password');

                if ($registrationEnabled) {
                    LivewireCompat::pageRoute('/register', $registerClass)->name('auth.register');
                }

                Route::post('/logout', function () use ($guard, $panelId) {
                    event(new UserLoggedOut($panelId, $guard, request()->ip()));
                    auth()->guard($guard)->logout();
                    request()->session()->invalidate();
                    request()->session()->regenerateToken();

                    return redirect()->route("panel.{$panelId}.auth.login");
                })->name('auth.logout');
            });
    }
}
