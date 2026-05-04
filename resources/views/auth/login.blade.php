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
        @if ($hasForgotRoute ?? false)
            <a href="{{ route("panel.{$panelId}.auth.forgot-password") }}">{{ __('panel::messages.forgot_password') }}</a>
        @endif
    </div>
    @if ($hasRegisterRoute ?? false)
        <div class="auth-footer">
            <a href="{{ route("panel.{$panelId}.auth.register") }}">{{ __('panel::messages.create_account') }}</a>
        </div>
    @endif
</div>
