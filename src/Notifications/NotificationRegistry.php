<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Notifications;

final class NotificationRegistry
{
    private array $providers = [];

    public function register(string $panelId, NotificationProviderInterface $provider): void
    {
        $this->providers[$panelId] = $provider;
    }

    public function has(string $panelId): bool
    {
        return isset($this->providers[$panelId]);
    }

    public function resolve(string $panelId): ?NotificationProviderInterface
    {
        return $this->providers[$panelId] ?? null;
    }
}
