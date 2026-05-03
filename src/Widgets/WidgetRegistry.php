<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

final class WidgetRegistry
{
    /** @var array<string, string> */
    private array $widgets = [];

    public function register(string $name, string $class): void
    {
        $this->widgets[$name] = $class;
    }

    /** @return array<string, string> */
    public function all(): array
    {
        return $this->widgets;
    }

    public function has(string $name): bool
    {
        return isset($this->widgets[$name]);
    }
}
