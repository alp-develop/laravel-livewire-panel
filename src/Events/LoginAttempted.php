<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

final class LoginAttempted extends PanelEvent
{
    public function __construct(
        string $panelId,
        public readonly string $email,
        public readonly bool $successful,
        public readonly string $guard,
        ?string $ip = null,
    ) {
        parent::__construct($panelId, $ip);
    }
}
