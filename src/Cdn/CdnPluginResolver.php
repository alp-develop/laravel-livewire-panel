<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Cdn;

/**
 * Resolves CDN assets for a panel config, applying route-matching rules.
 *
 * Results are memoized per CDN config hash + path to avoid redundant
 * serialization and normalization on repeated calls within a request.
 */
final class CdnPluginResolver
{
    /** @var array<string, array{css: list<array{url: string, integrity: string}>, js: list<array{url: string, integrity: string}>}> */
    private array $memo = [];

    /**
     * @param  array<string, mixed> $panelConfig
     * @return array{css: list<array{url: string, integrity: string}>, js: list<array{url: string, integrity: string}>}
     */
    public function resolveAssets(array $panelConfig, string $currentPath): array
    {
        $key = md5(serialize($panelConfig['cdn'] ?? [])) . '|' . $currentPath;

        if (isset($this->memo[$key])) {
            return $this->memo[$key];
        }

        $plugins = $panelConfig['cdn'] ?? [];
        $css     = [];
        $js      = [];

        foreach ($plugins as $plugin) {
            if (! $this->appliesToPath($plugin['routes'] ?? [], $currentPath)) {
                continue;
            }

            foreach ($plugin['css'] ?? [] as $entry) {
                $css[] = $this->normalizeEntry($entry);
            }

            foreach ($plugin['js'] ?? [] as $entry) {
                $js[] = $this->normalizeEntry($entry);
            }
        }

        return $this->memo[$key] = ['css' => $css, 'js' => $js];
    }

    /** @return array{url: string, integrity: string} */
    private function normalizeEntry(mixed $entry): array
    {
        if (is_array($entry)) {
            return ['url' => (string) ($entry['url'] ?? ''), 'integrity' => (string) ($entry['integrity'] ?? '')];
        }

        return ['url' => (string) $entry, 'integrity' => ''];
    }

    /** @param list<string> $routes */
    private function appliesToPath(array $routes, string $currentPath): bool
    {
        return $this->isApplicable($routes, $currentPath);
    }

    /** @param list<string> $routes */
    public function isApplicable(array $routes, string $currentPath): bool
    {
        if ($routes === []) {
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
