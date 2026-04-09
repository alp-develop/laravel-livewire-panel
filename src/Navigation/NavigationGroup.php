<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Navigation;

use AlpDevelop\LivewirePanel\Modules\NavigationItem;

final class NavigationGroup
{
    /** @param NavigationItem[] $children */
    public function __construct(
        public readonly string $label,
        public readonly array $children = [],
        public readonly string $icon = '',
        public readonly string $permission = '',
        public readonly string|array $roles = [],
    ) {}
}
