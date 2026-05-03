<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Cdn;

final class CdnManager implements CdnManagerInterface
{
    /** @var array<string, array<string, mixed>> */
    private static array $registered = [];

    /** @param array<string, mixed> $definition */
    public static function register(string $alias, array $definition): void
    {
        self::$registered[$alias] = $definition;
    }

    /** @return array<string, array<string, mixed>> */
    public static function registered(): array
    {
        return self::$registered;
    }

    /**
     * @param  array<string, mixed> $panelConfig
     * @return array{css: list<array{url: string, integrity: string}>, js: list<array{url: string, integrity: string}>}
     */
    public function resolveForPanel(array $panelConfig, string $currentPath): array
    {
        return app(CdnPluginResolver::class)->resolveAssets($panelConfig, $currentPath);
    }

    /**
     * @param  array<string, mixed> $panelConfig
     * @return list<string>
     */
    public function activeAliases(array $panelConfig, string $currentPath): array
    {
        $cdnEntries = $panelConfig['cdn'] ?? [];
        $resolver   = app(CdnPluginResolver::class);
        $active     = [];

        foreach ($cdnEntries as $alias => $config) {
            $routes = $config['routes'] ?? [];
            if ($resolver->isApplicable($routes, $currentPath)) {
                $active[] = is_string($alias) ? $alias : 'library';
            }
        }

        return $active;
    }

    /** @param array<string, mixed> $panelConfig */
    public function renderCssLinks(array $panelConfig, string $currentPath): string
    {
        $assets = $this->resolveForPanel($panelConfig, $currentPath);
        $output = '';

        foreach ($assets['css'] as $entry) {
            $url = e($entry['url']);
            $sri = $entry['integrity'] !== '' ? ' integrity="' . e($entry['integrity']) . '" crossorigin="anonymous"' : '';
            $output .= '<link rel="stylesheet" href="' . $url . '"' . $sri . '>' . "\n";
        }

        return $output;
    }

    /** @param array<string, mixed> $panelConfig */
    public function renderJsScripts(array $panelConfig, string $currentPath): string
    {
        $assets = $this->resolveForPanel($panelConfig, $currentPath);
        $output = '';

        foreach ($assets['js'] as $entry) {
            $url = e($entry['url']);
            $sri = $entry['integrity'] !== '' ? ' integrity="' . e($entry['integrity']) . '" crossorigin="anonymous"' : '';
            $output .= '<script src="' . $url . '"' . $sri . '></script>' . "\n";
        }

        return $output;
    }
}
