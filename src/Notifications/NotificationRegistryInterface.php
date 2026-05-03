<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Notifications;

interface NotificationRegistryInterface
{
    public function register(string $panelId, NotificationProviderInterface $provider): void;

    public function has(string $panelId): bool;

    public function resolve(string $panelId): ?NotificationProviderInterface;
}
