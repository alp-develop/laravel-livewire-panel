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
        @if ($hasLoginRoute ?? false)
            <a href="{{ route("panel.{$panelId}.auth.login") }}">{{ __('panel::messages.already_have_account') }}</a>
        @endif
    </div>
</div>
