<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Search;

final class SearchRegistry
{
    private array $providers = [];

    public function register(string $panelId, SearchProviderInterface $provider): void
    {
        $this->providers[$panelId][] = $provider;
    }

    public function forPanel(string $panelId): array
    {
        return $this->providers[$panelId] ?? [];
    }

    public function search(string $query, string $panelId): array
    {
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

        return $groups;
    }
}
