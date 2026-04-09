<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Cdn;

final class CdnPluginResolver
{
    public function resolveAssets(array $panelConfig, string $currentPath): array
    {
        $plugins = $panelConfig['cdn'] ?? [];
        $css     = [];
        $js      = [];

        foreach ($plugins as $plugin) {
            if (! $this->appliesToPath($plugin['routes'] ?? [], $currentPath)) {
                continue;
            }

            foreach ($plugin['css'] ?? [] as $url) {
                $css[] = $url;
            }

            foreach ($plugin['js'] ?? [] as $url) {
                $js[] = $url;
            }
        }

        return ['css' => $css, 'js' => $js];
    }

    private function appliesToPath(array $routes, string $currentPath): bool
    {
        return $this->isApplicable($routes, $currentPath);
    }

    public function isApplicable(array $routes, string $currentPath): bool
    {
        if (empty($routes)) {
            return true;
        }

        $currentPath = ltrim($currentPath, '/');

        foreach ($routes as $pattern) {
            $pattern = ltrim($pattern, '/');

            if (fnmatch($pattern, $currentPath)) {
                return true;
            }
        }

        return false;
    }
}
