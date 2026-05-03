<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

final class PanelPortalBuilder
{
    public function __construct(private readonly string $panelId) {}

    /** @param array<string, mixed> $parameters */
    public function route(string $routeName, array $parameters = []): string
    {
        return panel_route($this->panelId, $routeName, $parameters);
    }

    public function home(): string
    {
        return $this->route('home');
    }
}
