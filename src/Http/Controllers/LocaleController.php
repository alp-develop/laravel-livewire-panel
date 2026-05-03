<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Http\Controllers;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\PanelContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocaleController
{
    public function __construct(
        private readonly PanelContext $context,
        private readonly PanelConfig  $config,
    ) {}

    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        if (!preg_match('/^[a-zA-Z]{2}([_-][a-zA-Z]{2,4})?$/', $locale)) {
            return redirect()->back();
        }

        $panelId    = $this->context->resolved() ? $this->context->get() : '';
        $locales    = [];

        if ($panelId !== '' && $this->config->has($panelId)) {
            $panelConfig = $this->config->get($panelId);
            $locales     = array_keys($panelConfig['layout']['locales'] ?? []);
        }

        if ($locales !== [] && !in_array($locale, $locales, true)) {
            return redirect()->back();
        }

        $request->session()->put('panel_locale', $locale);
        app()->setLocale($locale);

        return redirect()->back();
    }
}
