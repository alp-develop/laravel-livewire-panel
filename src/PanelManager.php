<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Notifications\NotificationRegistryInterface;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use AlpDevelop\LivewirePanel\Search\SearchRegistryInterface;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;

final class PanelManager
{
    public function __construct(
        private readonly PanelContext                  $context,
        private readonly PanelResolver                 $resolver,
        private readonly PanelKernel                   $kernel,
        private readonly SearchRegistryInterface       $searchRegistry,
        private readonly NotificationRegistryInterface $notificationRegistry,
    ) {}

    public function currentId(): string
    {
        if ($this->context->resolved()) {
            return $this->context->get();
        }

        return $this->resolver->resolveFromRequest(request());
    }

    public function for(string $panelId): PanelPortalBuilder
    {
        return new PanelPortalBuilder($panelId);
    }

    /** @param array<string, mixed> $parameters */
    public function route(string $routeName, array $parameters = []): string
    {
        return panel_route($this->currentId(), $routeName, $parameters);
    }

    public function kernel(): PanelKernel
    {
        return $this->kernel;
    }

    public function themes(): ThemeRegistry
    {
        return $this->kernel->themes();
    }

    public function modules(): ModuleRegistry
    {
        return $this->kernel->modules();
    }

    public function plugins(): PluginRegistry
    {
        return $this->kernel->plugins();
    }

    public function widgets(): WidgetRegistry
    {
        return $this->kernel->widgets();
    }

    public function search(): SearchRegistryInterface
    {
        return $this->searchRegistry;
    }

    public function notifications(): NotificationRegistryInterface
    {
        return $this->notificationRegistry;
    }

    public function clearCaches(): void
    {
        $this->searchRegistry->clearCache();
        PanelRenderer::clearCache();
    }
}
