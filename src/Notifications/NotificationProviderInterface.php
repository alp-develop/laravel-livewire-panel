<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Notifications;

interface NotificationProviderInterface
{
    public function count(string $panelId): int;

    public function items(string $panelId, int $limit = 10): array;

    public function markAsRead(string $id, string $panelId): void;

    public function markAllAsRead(string $panelId): void;
}
