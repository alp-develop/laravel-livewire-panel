<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

final class UserDeleted extends PanelEvent
{
    public function __construct(
        string $panelId,
        public readonly int $userId,
        public readonly ?int $deletedBy = null,
        ?string $ip = null,
    ) {
        parent::__construct($panelId, $ip);
    }
}
