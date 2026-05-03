<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Plugins;

final class PluginRegistry
{
    /** @var list<string> */
    private array $classes = [];

    /** @var list<PluginInterface> */
    private array $instances = [];

    public function register(string $class): void
    {
        $this->classes[] = $class;
    }

    public function boot(): void
    {
        foreach ($this->classes as $class) {
            /** @var PluginInterface $plugin */
            $plugin            = app($class);
            $this->instances[] = $plugin;
            $plugin->beforeBoot();
        }
    }

    public function afterBoot(): void
    {
        foreach ($this->instances as $plugin) {
            $plugin->afterBoot();
        }
    }

    /** @return list<PluginInterface> */
    public function allInstances(): array
    {
        return $this->instances;
    }

    /** @return list<string> */
    public function all(): array
    {
        return $this->classes;
    }
}

