<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use AlpDevelop\LivewirePanel\Events\LoginAttempted;
use AlpDevelop\LivewirePanel\Events\UserLoggedIn;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class AbstractLoginComponent extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    public bool $remember = false;

    #[Locked]
    public string $panelId = '';

    public function mount(): void
    {
        $resolver      = app(PanelResolver::class);
        $this->panelId = $resolver->resolveFromRequest(request());
        $panelConfig   = $resolver->resolveById($this->panelId);
        $guard         = (string) ($panelConfig['guard'] ?? 'web');

        if (auth()->guard($guard)->check()) {
            $this->redirectToHome();
        }
    }

    public function login(): void
    {
        $this->validate();

        $throttleKey = 'panel-login:' . sha1(mb_strtolower($this->email)) . ':' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('auth.throttle', ['seconds' => $seconds]));

            return;
        }

        $resolver    = app(PanelResolver::class);
        $panelConfig = $resolver->resolveById($this->panelId);
        $guard       = (string) ($panelConfig['guard'] ?? 'web');

        if (!auth()->guard($guard)->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember
        )) {
            $this->password = '';
            RateLimiter::hit($throttleKey, 60);
            event(new LoginAttempted($this->panelId, $this->email, false, $guard, request()->ip()));
            $this->addError('email', __('auth.failed'));

            return;
        }

        RateLimiter::clear($throttleKey);
        request()->session()->regenerate();

        $user = auth()->guard($guard)->user();
        event(new LoginAttempted($this->panelId, $this->email, true, $guard, request()->ip()));
        event(new UserLoggedIn($this->panelId, $user !== null ? (int) $user->getAuthIdentifier() : 0, $guard, request()->ip()));

        $this->redirectToHome();
    }

    protected function redirectToHome(): void
    {
        $resolver    = app(PanelResolver::class);
        $panelConfig = $resolver->resolveById($this->panelId);
        $prefix      = trim((string) ($panelConfig['prefix'] ?? ''), '/');

        $this->redirect('/' . $prefix);
    }

    abstract public function render(): View;
}
