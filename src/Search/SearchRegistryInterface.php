<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Search;

interface SearchRegistryInterface
{
    public function register(string $panelId, SearchProviderInterface $provider): void;

    /** @return list<SearchProviderInterface> */
    public function forPanel(string $panelId): array;

    /** @return list<array<string, mixed>> */
    public function search(string $query, string $panelId): array;

    public function clearCache(): void;
}
