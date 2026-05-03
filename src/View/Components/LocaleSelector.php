<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Components;

use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

final class LocaleSelector extends Component
{
    public bool $enabled;
    /** @var array<string, string> */
    public array $available;
    public string $current;

    public function __construct(
        public string $variant = 'dropdown',
    ) {
        $context  = app(PanelContext::class);
        $resolver = app(PanelResolver::class);

        if ($context->resolved()) {
            $panelConfig = $resolver->resolveById($context->get());
        } else {
            try {
                $panelId     = $resolver->resolveFromRequest(request());
                $panelConfig = $resolver->resolveById($panelId);
            } catch (\Throwable) {
                $panelConfig = [];
            }
        }

        $localeConfig    = $panelConfig['locale'] ?? [];
        $this->enabled   = (bool) ($localeConfig['enabled'] ?? false);
        $this->available = $localeConfig['available'] ?? [];
        $this->current   = app()->getLocale();
    }

    public function shouldRender(): bool
    {
        return $this->enabled && count($this->available) > 1;
    }

    public function render(): View
    {
        return view('panel::components.locale-selector');
    }
}
