<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        if (!preg_match('/^[a-zA-Z]{2}([_-][a-zA-Z]{2,4})?$/', $locale)) {
            return redirect()->back();
        }

        $request->session()->put('panel_locale', $locale);
        app()->setLocale($locale);

        return redirect()->back();
    }
}
