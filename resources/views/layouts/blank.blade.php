@panelLayoutConfig
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @panelHtmlAttributes>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __($title ?? 'Panel') }}</title>
    @panelCssAssets('blank')
    @panelCssVars
    @livewireStyles
    @stack('styles')
</head>
<body>
@php
    $__panelId = \AlpDevelop\LivewirePanel\PanelRenderer::resolvePanelId();
    $__panelCfg = app(\AlpDevelop\LivewirePanel\PanelKernel::class)->config()->get($__panelId);
    $__showLocale = $__panelCfg['locale']['enabled'] ?? false;
    $__showDark = $__panelLayout['dark_mode'] ?? false;
@endphp
@if ($__showLocale || $__showDark)
<div style="position:fixed;top:1rem;right:1rem;z-index:100;display:flex;align-items:center;gap:0.5rem">
    @if ($__showDark)
        <x-panel::dark-mode-toggle />
    @endif
    @if ($__showLocale)
        <x-panel::locale-selector />
    @endif
</div>
@endif
{{ $slot }}
@panelJsAssets
@livewireScripts
@stack('scripts')
</body>
</html>
