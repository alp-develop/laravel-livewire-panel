<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Config;

use AlpDevelop\LivewirePanel\Exceptions\PanelStyleNotFoundException;

final class PanelStyleConfig
{
    private array $styles = [];

    public function loadFromDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*.php') ?: [] as $file) {
            $config = require $file;

            if (!is_array($config)) {
                continue;
            }

            $id = $config['id'] ?? basename($file, '.php');
            $this->styles[$id] = $config;
        }
    }

    public function get(string $id): array
    {
        if (!isset($this->styles[$id])) {
            throw new PanelStyleNotFoundException("Panel style [{$id}] not found in config/panel/.");
        }

        return $this->styles[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->styles[$id]);
    }

    public function all(): array
    {
        return $this->styles;
    }
}
