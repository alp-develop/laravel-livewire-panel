<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

final class ModuleRegistry
{
    private array $modules = [];

    public function register(string $panelId, string $class): void
    {
        $this->modules[$panelId][] = $class;
    }

    public function forPanel(string $panelId): array
    {
        return $this->modules[$panelId] ?? [];
    }

    public function all(): array
    {
        return $this->modules;
    }
}
