<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use AlpDevelop\LivewirePanel\PanelRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class DarkModeToggle extends Component
{
    public bool $enabled;

    public function __construct()
    {
        try {
            $layout = PanelRenderer::layoutConfig();
            $this->enabled = (bool) ($layout['dark_mode'] ?? false);
        } catch (\Throwable) {
            $this->enabled = false;
        }
    }

    public function shouldRender(): bool
    {
        return $this->enabled;
    }

    public function render(): View
    {
        return view('panel::components.dark-mode-toggle');
    }
}
