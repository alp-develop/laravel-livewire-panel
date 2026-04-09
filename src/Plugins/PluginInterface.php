<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Plugins;

interface PluginInterface
{
    public function id(): string;

    public function beforeBoot(): void;

    public function afterBoot(): void;

    public function registerNavigation(): array;

    public function registerWidgets(): array;
}
