<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Plugins;

abstract class AbstractPlugin implements PluginInterface
{
    public function beforeBoot(): void {}

    public function afterBoot(): void {}

    public function registerNavigation(): array
    {
        return [];
    }

    public function registerWidgets(): array
    {
        return [];
    }
}
