<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

final class UserUpdated extends PanelEvent
{
    public function __construct(
        string $panelId,
        public readonly int $userId,
        public readonly ?int $updatedBy = null,
        ?string $ip = null,
    ) {
        parent::__construct($panelId, $ip);
    }
}
