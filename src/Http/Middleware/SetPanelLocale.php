<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetPanelLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('panel_locale');

        if (is_string($locale) && preg_match('/^[a-zA-Z]{2}([_-][a-zA-Z]{2,4})?$/', $locale)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
