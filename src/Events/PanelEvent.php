<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Events;

abstract class PanelEvent
{
    public readonly string $timestamp;

    public function __construct(
        public readonly string $panelId,
        public readonly ?string $ip = null,
    ) {
        $this->timestamp = now()->toIso8601String();
    }
}
