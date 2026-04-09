@panelLayoutConfig
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Page' }}</title>
    @panelCssAssets('public')
    @panelCssVars
    @livewireStyles
</head>
<body>
    {{ $header ?? '' }}

    @if(!isset($header))
    <div class="panel-public-topbar">
        <a href="/" class="panel-public-brand">
            <span class="panel-public-brand-icon">
                <x-panel::icon name="layer-group" size="18" />
            </span>
            <span>{{ $__panelLayout['sidebar_header_text'] ?? 'Panel' }}</span>
        </a>
        <nav class="panel-public-nav">
            {{ $nav ?? '' }}
        </nav>
    </div>
    @endif

    <div class="panel-public-body">
        {{ $slot }}
    </div>

    {{ $footer ?? '' }}
@panelJsAssets
@livewireScripts
</body>
</html>
