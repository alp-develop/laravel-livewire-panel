<div class="auth-card">
    <h2 class="auth-title">{{ __('panel::messages.reset_password_title') }}</h2>
    <p class="auth-subtitle">{{ __('panel::messages.reset_password_subtitle') }}</p>

    <form wire:submit="submit">
        <div class="auth-field">
            <label class="auth-label" for="email">{{ __('panel::messages.email') }}</label>
            <input
                id="email"
                type="email"
                class="auth-input {{ $errors->has('email') ? 'invalid' : '' }}"
                wire:model="email"
                autocomplete="email"
                readonly
            />
            @error('email') <div class="auth-error">{{ $message }}</div> @enderror
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
