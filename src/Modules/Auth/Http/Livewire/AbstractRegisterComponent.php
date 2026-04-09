<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use AlpDevelop\LivewirePanel\Events\UserRegistered;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class AbstractRegisterComponent extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|max:255')]
    public string $email = '';

    #[Rule('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

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

    public function register(): void
    {
        $throttleKey = 'panel-register:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', __('auth.throttle', ['seconds' => $seconds]));
            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $resolver    = app(PanelResolver::class);
        $panelId     = $resolver->resolveFromRequest(request());
        $panelConfig = $resolver->resolveById($panelId);
        $guard       = (string) ($panelConfig['guard'] ?? 'web');

        $provider   = config("auth.guards.{$guard}.provider");
        $modelClass = config("auth.providers.{$provider}.model");
        $table      = (new $modelClass)->getTable();

        $this->validate([
            'name'     => 'required|string|max:255',
            'email'    => "required|email|max:255|unique:{$table},email",
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = $modelClass::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => Hash::make($this->password),
        ]);

        auth()->guard($guard)->login($user);

        request()->session()->regenerate();

        event(new UserRegistered($panelId, (int) $user->getKey(), $this->email, $guard, request()->ip()));

        $this->redirectToHome();
    }

    protected function redirectToHome(): void
    {
        $resolver    = app(PanelResolver::class);
        $panelId     = $resolver->resolveFromRequest(request());
        $panelConfig = $resolver->resolveById($panelId);
        $prefix      = trim((string) ($panelConfig['prefix'] ?? ''), '/');

        $this->redirect('/' . $prefix);
    }

    abstract public function render(): View;
}
