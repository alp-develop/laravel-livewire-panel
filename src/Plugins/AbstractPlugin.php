<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Plugins;

abstract class AbstractPlugin implements PluginInterface
{
    public function beforeBoot(): void {}

    public function afterBoot(): void {}

    /** @return array<string, list<mixed>> */
    public function registerNavigation(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function registerWidgets(): array
    {
        return [];
    }
}
