<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException;
use Illuminate\Http\Request;

final class PanelResolver
{
    public function __construct(private readonly PanelConfig $config) {}

    public function resolveFromRequest(Request $request): string
    {
        $path      = trim($request->getPathInfo(), '/');
        $rootPanel = null;

        foreach ($this->config->all() as $id => $panel) {
            $prefix = trim((string) ($panel['prefix'] ?? $id), '/');

            if ($prefix === '') {
                $rootPanel = $id;
                continue;
            }

            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $id;
            }
        }

        return $rootPanel ?? $this->config->default();
    }

    /** @return array<string, mixed> */
    public function resolveById(string $id): array
    {
        return $this->config->get($id);
    }

    public function hasPanel(string $id): bool
    {
        return $this->config->has($id);
    }
}
