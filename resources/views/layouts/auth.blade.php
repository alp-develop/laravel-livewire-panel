@panelLayoutConfig
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __($title ?? $__panelLayout['title'] ?? 'Acceso') }}</title>
    @panelCssAssets('auth')
    @panelCssVars
    @livewireStyles
    @stack('styles')
</head>
<body>
@if ($__panelLayout['locale_show_on_auth'] || $__panelLayout['dark_mode_show_on_auth'])
<div style="position:fixed;top:1rem;right:1rem;z-index:100;display:flex;align-items:center;gap:0.5rem">
    @if ($__panelLayout['dark_mode_show_on_auth'])
        <x-panel::dark-mode-toggle />
    @endif
    @if ($__panelLayout['locale_show_on_auth'])
        <x-panel::locale-selector />
    @endif
</div>
@endif
<div class="auth-container">
    <div class="auth-brand">
        <div class="auth-brand-icon">
            <x-panel::icon name="layer-group" size="28" />
        </div>
        <div class="auth-brand-name">Panel Admin</div>
    </div>

    {{ $slot }}
</div>
@panelJsAssets
@stack('scripts')
@livewireScripts
</body>
</html>
