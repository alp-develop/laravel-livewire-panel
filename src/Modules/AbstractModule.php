<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

abstract class AbstractModule implements ModuleInterface
{
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

    public function permissions(): array
    {
        return [];
    }

    public function publicRoutes(): void
    {
    }

    public function navigationItems(): array
    {
        return [];
    }

    public function userMenuItems(): array
    {
        return [];
    }
}
