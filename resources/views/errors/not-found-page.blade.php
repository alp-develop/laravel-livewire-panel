<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('panel::messages.not_found_title') }} &mdash; {{ config('app.name', 'Panel') }}</title>
    @panelCssVars
    @panelCssAssets('auth')
    <style>
        *, *::before, *::after { box-sizing: border-box }
        body { margin: 0; padding: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--panel-auth-bg, #f4f6f9); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif }
    </style>
</head>
<body>
    @include('panel::errors.not-found')
</body>
</html>
