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
        @if ($hasLoginRoute ?? false)
            <a href="{{ route("panel.{$panelId}.auth.login") }}">
                <x-panel::icon name="arrow-left" size="14" style="vertical-align:-2px;margin-right:2px" />
                {{ __('panel::messages.back_to_sign_in') }}
            </a>
        @endif
    </div>
</div>
