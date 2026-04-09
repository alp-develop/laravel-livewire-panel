<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules;

final class NavigationItem
{
    public function __construct(
        public readonly string $label,
        public readonly string $route,
        public readonly string $icon = '',
        public readonly string $permission = '',
        public readonly string|array $roles = [],
        public readonly ?\Closure $badge = null,
        public readonly string $description = '',
        public readonly string $keywords = '',
    ) {}
}
