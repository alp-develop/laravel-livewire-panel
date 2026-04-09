<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Concerns;

use AlpDevelop\LivewirePanel\PanelKernel;
use AlpDevelop\LivewirePanel\Themes\ThemeInterface;

trait ResolvesActiveTheme
{
    private function resolveThemeId(): string
    {
        $kernel  = app(PanelKernel::class);
        $panelId = $kernel->config()->default();
        $panel   = $kernel->config()->get($panelId);

        return (string) ($panel['theme'] ?? 'bootstrap5');
    }

    private function resolveTheme(): ThemeInterface
    {
        $kernel  = app(PanelKernel::class);
        $themeId = $this->resolveThemeId();

        return $kernel->themes()->resolve($themeId);
    }
}
