<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Search;

interface SearchProviderInterface
{
    public function category(): string;

    public function icon(): string;

    /** @return list<array<string, mixed>> */
    public function search(string $query, string $panelId): array;
}
