<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

interface ModuleInterface
{
    public function id(): string;

    /** @return list<mixed> */
    public function navigationItems(): array;

    /** @return list<array<string, mixed>> */
    public function userMenuItems(): array;

    public function routes(): void;

    public function publicRoutes(): void;

    /** @return list<string> */
    public function permissions(): array;
}
