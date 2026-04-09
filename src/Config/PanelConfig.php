<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Config;

use AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException;

final class PanelConfig
{
    private array $panels;

    public function __construct(private readonly array $raw)
    {
        $this->panels = $this->normalizePanels($raw);
    }

    private function normalizePanels(array $config): array
    {
        if (isset($config['panels'])) {
            return $config['panels'];
        }

        $id = $config['id'] ?? 'default';

        return [$id => array_merge($config, ['id' => $id])];
    }

    public function all(): array
    {
        return $this->panels;
    }

    public function get(string $id): array
    {
        if (!isset($this->panels[$id])) {
            throw new PanelNotFoundException("Panel [{$id}] not found in configuration.");
        }

        return $this->panels[$id];
    }

    public function default(): string
    {
        return $this->raw['default'] ?? (string) array_key_first($this->panels);
    }

    public function has(string $id): bool
    {
        return isset($this->panels[$id]);
    }

    public function ids(): array
    {
        return array_keys($this->panels);
    }

    public function raw(): array
    {
        return $this->raw;
    }
}
