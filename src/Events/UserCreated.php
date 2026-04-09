<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

final class UserCreated extends PanelEvent
{
    public function __construct(
        string $panelId,
        public readonly int $userId,
        public readonly string $email,
        public readonly ?int $createdBy = null,
        ?string $ip = null,
    ) {
        parent::__construct($panelId, $ip);
    }
}
