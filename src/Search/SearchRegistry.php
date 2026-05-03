<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Search;

/**
 * Registry for search providers per panel.
 *
 * Providers are registered per panel ID and queried in registration order.
 * Results from all providers are merged into a grouped result set.
 */
final class SearchRegistry implements SearchRegistryInterface
{
    /** @var array<string, list<SearchProviderInterface>> */
    private array $providers = [];

    /** @var array<string, list<array<string, mixed>>> */
    private array $cache = [];

    public function register(string $panelId, SearchProviderInterface $provider): void
    {
        $this->providers[$panelId][] = $provider;
    }

    /** @return list<SearchProviderInterface> */
    public function forPanel(string $panelId): array
    {
        return $this->providers[$panelId] ?? [];
    }

    /** @return list<array<string, mixed>> */
    public function search(string $query, string $panelId): array
    {
        $key = $panelId . ':' . $query;

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $groups = [];

        foreach ($this->forPanel($panelId) as $provider) {
            $results = $provider->search($query, $panelId);

            if (empty($results)) {
                continue;
            }

            $groups[] = [
                'category' => $provider->category(),
                'icon'     => $provider->icon(),
                'items'    => $results,
            ];
        }

        $this->cache[$key] = $groups;

        return $groups;
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }
}
