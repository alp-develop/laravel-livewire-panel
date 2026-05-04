@panelLayoutConfig
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" @panelHtmlAttributes>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __($title ?? $__panelLayout['title'] ?? 'Panel') }}</title>
    @panelCssAssets('app')
    @panelCssVars
    @livewireStyles
    @stack('styles')
</head>
<body>
<div class="panel-sidebar-overlay" onclick="closeMobileSidebar()"></div>

@if ($__panelLayout['show_search'])
    @livewire('panel-search')
@endif

<div class="panel-layout">
    @livewire(panel_component('sidebar'))

    <div class="panel-main">
        @livewire(panel_component('navbar'), ['title' => $title ?? ''])

        <main class="panel-content">
            {{ $slot }}
        </main>
    </div>
</div>

@if($__panelLayout['back_to_top'])
<button class="panel-back-to-top" id="panelBackToTop" onclick="document.querySelector('.panel-main').scrollTo({top:0,behavior:'smooth'})" title="Back to top">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
</button>
@endif

@panelJsAssets('app')
@livewireScripts
@stack('scripts')
</body>
</html>
