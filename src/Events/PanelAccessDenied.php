<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

final class PanelAccessDenied extends PanelEvent
{
    public function __construct(
        string $panelId,
        public readonly ?int $userId = null,
        public readonly string $reason = 'unauthorized',
        ?string $ip = null,
    ) {
        parent::__construct($panelId, $ip);
    }
}
