@panelLayoutConfig
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Acceso' }}</title>
    @panelCssAssets('auth')
    @panelCssVars
    @livewireStyles
    @stack('styles')
</head>
<body>
@php
    $__panelId = \AlpDevelop\LivewirePanel\PanelRenderer::resolvePanelId();
    $__panelCfg = app(\AlpDevelop\LivewirePanel\PanelKernel::class)->config()->get($__panelId);
    $__showLocaleOnAuth = ($__panelCfg['locale']['enabled'] ?? false) && ($__panelCfg['locale']['show_on_auth'] ?? false);
    $__showDarkOnAuth = ($__panelLayout['dark_mode'] ?? false) && ($__panelLayout['dark_mode_show_on_auth'] ?? false);
@endphp
@if ($__showLocaleOnAuth || $__showDarkOnAuth)
<div style="position:fixed;top:1rem;right:1rem;z-index:100;display:flex;align-items:center;gap:0.5rem">
    @if ($__showDarkOnAuth)
        <x-panel::dark-mode-toggle />
    @endif
    @if ($__showLocaleOnAuth)
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
