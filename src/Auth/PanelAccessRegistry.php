<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth;

final class PanelAccessRegistry
{
    private array $checks = [];

    public function for(string $panelId, callable $check): void
    {
        $this->checks[$panelId] = $check;
    }

    public function has(string $panelId): bool
    {
        return isset($this->checks[$panelId]);
    }

    public function check(string $panelId, mixed $user): bool
    {
        return (bool) ($this->checks[$panelId])($user);
    }

    public function findPanel(mixed $user): ?string
    {
        foreach ($this->checks as $panelId => $check) {
            if ($check($user)) {
                return $panelId;
            }
        }

        return null;
    }
}
