<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Navigation;

use AlpDevelop\LivewirePanel\Modules\NavigationItem;

final class NavigationRegistry
{
    private array $items = [];

    public function add(string $panelId, NavigationItem $item): void
    {
        $this->items[$panelId][] = $item;
    }

    public function addGroup(string $panelId, NavigationGroup $group): void
    {
        $this->items[$panelId][] = $group;
    }

    public function loadFromConfig(string $panelId, array $config): void
    {
        foreach ($config as $entry) {
            if (!empty($entry['children'])) {
                $children = [];
                foreach ($entry['children'] as $child) {
                    $children[] = new NavigationItem(
                        label: $child['label'] ?? '',
                        route: $child['route'] ?? '',
                        icon: $child['icon'] ?? '',
                        permission: $child['permission'] ?? '',
                        roles: $child['roles'] ?? [],
                    );
                }
                $this->items[$panelId][] = new NavigationGroup(
                    label: $entry['label'] ?? '',
                    children: $children,
                    icon: $entry['icon'] ?? '',
                    permission: $entry['permission'] ?? '',
                    roles: $entry['roles'] ?? [],
                );
            } else {
                $this->items[$panelId][] = new NavigationItem(
                    label: $entry['label'] ?? '',
                    route: $entry['route'] ?? '',
                    icon: $entry['icon'] ?? '',
                    permission: $entry['permission'] ?? '',
                    roles: $entry['roles'] ?? [],
                );
            }
        }
    }

    /** @return array<NavigationItem|NavigationGroup> */
    public function forPanel(string $panelId): array
    {
        return $this->items[$panelId] ?? [];
    }
}
