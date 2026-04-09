<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

final class UserLoggedOut extends PanelEvent
{
    public function __construct(
        string $panelId,
        public readonly string $guard,
        ?string $ip = null,
    ) {
        parent::__construct($panelId, $ip);
    }
}
