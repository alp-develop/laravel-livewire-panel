<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Plugins;

interface PluginInterface
{
    public function id(): string;

    public function beforeBoot(): void;

    public function afterBoot(): void;

    /** @return array<string, list<mixed>> */
    public function registerNavigation(): array;

    /** @return array<string, string> */
    public function registerWidgets(): array;
}
