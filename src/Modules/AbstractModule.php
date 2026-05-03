<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

/**
 * Base implementation for panel modules.
 *
 * Extend this class and implement `id()` and `routes()` to register a module.
 * Override `navigationItems()`, `userMenuItems()`, or `permissions()` as needed.
 *
 * @see ModuleInterface
 */
abstract class AbstractModule implements ModuleInterface
{
    /** @param array<string, mixed> $panelConfig */
    public function __construct(protected readonly array $panelConfig) {}

    protected function panelId(): string
    {
        return $this->panelConfig['id'] ?? 'default';
    }

    protected function prefix(): string
    {
        return trim((string) ($this->panelConfig['prefix'] ?? ''), '/');
    }

    protected function guard(): string
    {
        return (string) ($this->panelConfig['guard'] ?? 'web');
    }

    /** @return list<string> */
    public function permissions(): array
    {
        return [];
    }

    public function publicRoutes(): void
    {
    }

    /** @return list<mixed> */
    public function navigationItems(): array
    {
        return [];
    }

    /** @return list<array<string, mixed>> */
    public function userMenuItems(): array
    {
        return [];
    }
}
