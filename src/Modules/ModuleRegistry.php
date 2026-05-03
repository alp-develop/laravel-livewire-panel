<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

final class ModuleRegistry
{
    /** @var array<string, list<string>> */
    private array $modules = [];

    public function register(string $panelId, string $class): void
    {
        $this->modules[$panelId][] = $class;
    }

    /** @return list<string> */
    public function forPanel(string $panelId): array
    {
        return $this->modules[$panelId] ?? [];
    }

    /** @return array<string, list<string>> */
    public function all(): array
    {
        return $this->modules;
    }
}
