<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Cdn;

final class CdnManager
{
    private static array $registered = [];

    public static function register(string $alias, array $definition): void
    {
        self::$registered[$alias] = $definition;
    }

    public static function registered(): array
    {
        return self::$registered;
    }

    public function resolveForPanel(array $panelConfig, string $currentPath): array
    {
        $resolver = new CdnPluginResolver();
        return $resolver->resolveAssets($panelConfig, $currentPath);
    }

    public function activeAliases(array $panelConfig, string $currentPath): array
    {
        $cdnEntries = $panelConfig['cdn'] ?? [];
        $resolver   = new CdnPluginResolver();
        $active     = [];

        foreach ($cdnEntries as $alias => $config) {
            $routes = $config['routes'] ?? [];
            if ($resolver->isApplicable($routes, $currentPath)) {
                $active[] = is_string($alias) ? $alias : 'library';
            }
        }

        return $active;
    }

    public function renderCssLinks(array $panelConfig, string $currentPath): string
    {
        $assets = $this->resolveForPanel($panelConfig, $currentPath);
        $output = '';

        foreach ($assets['css'] as $url) {
            $output .= '<link rel="stylesheet" href="' . e($url) . '">' . "\n";
        }

        return $output;
    }

    public function renderJsScripts(array $panelConfig, string $currentPath): string
    {
        $assets = $this->resolveForPanel($panelConfig, $currentPath);
        $output = '';

        foreach ($assets['js'] as $url) {
            $output .= '<script src="' . e($url) . '"></script>' . "\n";
        }

        return $output;
    }
}
