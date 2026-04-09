<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Concerns;

trait RedirectsToPanelRoute
{
    public function redirectToPanelRoute(string $panelId, string $routeName, array $parameters = []): void
    {
        $this->redirect(panel_route($panelId, $routeName, $parameters));
    }
}
