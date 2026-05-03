<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Themes;

use AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException;

final class ThemeRegistry
{
    /** @var array<string, string> */
    private array $themes    = [];
    /** @var array<string, ThemeInterface> */
    private array $instances = [];

    public function register(string $id, string $class): void
    {
        $this->themes[$id] = $class;
    }

    public function resolve(string $id): ThemeInterface
    {
        if (!isset($this->themes[$id])) {
            throw new PanelNotFoundException("Theme [{$id}] is not registered.");
        }

        if (!isset($this->instances[$id])) {
            $instance = new ($this->themes[$id])();
            assert($instance instanceof ThemeInterface);
            $this->instances[$id] = $instance;
        }

        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->themes[$id]);
    }

    public function get(string $id): string
    {
        return $this->themes[$id] ?? '';
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return $this->themes;
    }
}
