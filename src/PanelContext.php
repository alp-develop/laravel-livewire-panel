<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

final class PanelContext
{
    private string $panelId = '';

    public function set(string $panelId): void
    {
        $this->panelId = $panelId;
    }

    public function get(): string
    {
        return $this->panelId;
    }

    public function resolved(): bool
    {
        return $this->panelId !== '';
    }
}
