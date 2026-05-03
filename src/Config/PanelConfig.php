<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Config;

use AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException;

final class PanelConfig
{
    /** @var array<string, array<string, mixed>> */
    private array $panels;

    /** @param array<string, mixed> $raw */
    public function __construct(private readonly array $raw)
    {
        $this->panels = $this->normalizePanels($raw);
    }

    /**
     * @param  array<string, mixed> $config
     * @return array<string, array<string, mixed>>
     */
    private function normalizePanels(array $config): array
    {
        if (isset($config['panels'])) {
            $normalized = [];

            foreach ($config['panels'] as $key => $panel) {
                $id                = (string) ($panel['id'] ?? $key);
                $normalized[$key]  = array_merge($panel, ['id' => $id]);
            }

            return $normalized;
        }

        $id = $config['id'] ?? 'default';

        return [$id => array_merge($config, ['id' => $id])];
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        return $this->panels;
    }

    /** @return array<string, mixed> */
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

    /** @return list<string> */
    public function ids(): array
    {
        return array_keys($this->panels);
    }

    /** @return array<string, mixed> */
    public function raw(): array
    {
        return $this->raw;
    }
}
