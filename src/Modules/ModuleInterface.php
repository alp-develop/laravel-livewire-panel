<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

interface ModuleInterface
{
    public function id(): string;

    public function navigationItems(): array;

    public function userMenuItems(): array;

    public function routes(): void;

    public function publicRoutes(): void;

    public function permissions(): array;
}
