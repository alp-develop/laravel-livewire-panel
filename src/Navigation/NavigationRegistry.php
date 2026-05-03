<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Navigation;

use AlpDevelop\LivewirePanel\Modules\NavigationItem;

/**
 * Registry for sidebar and user menu navigation items across panels.
 *
 * Supports direct item registration, groups, and config-based loading.
 * Internally builds a route map for O(1) route-to-title lookups.
 */
final class NavigationRegistry
{
    /** @var array<string, list<\AlpDevelop\LivewirePanel\Modules\NavigationItem|\AlpDevelop\LivewirePanel\Navigation\NavigationGroup>> */
    private array $items = [];

    /** @var array<string, array<string, string>> */
    private array $routeMap = [];

    public function add(string $panelId, NavigationItem $item): void
    {
        $this->items[$panelId][] = $item;
        unset($this->routeMap[$panelId]);
    }

    public function addGroup(string $panelId, NavigationGroup $group): void
    {
        $this->items[$panelId][] = $group;
        unset($this->routeMap[$panelId]);
    }

    /** @param array<int, array<string, mixed>> $config */
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

        unset($this->routeMap[$panelId]);
    }

    /** @return array<string, string> */
    public function buildRouteMap(string $panelId): array
    {
        if (isset($this->routeMap[$panelId])) {
            return $this->routeMap[$panelId];
        }

        $map = [];

        foreach ($this->items[$panelId] ?? [] as $item) {
            if ($item instanceof NavigationGroup) {
                foreach ($item->children as $child) {
                    if ($child->route !== '') {
                        $map[$child->route] = $child->label;
                    }
                }
            } elseif ($item instanceof NavigationItem) {
                if ($item->route !== '') {
                    $map[$item->route] = $item->label;
                }
            }
        }

        return $this->routeMap[$panelId] = $map;
    }

    /** @return array<NavigationItem|NavigationGroup> */
    public function forPanel(string $panelId): array
    {
        return $this->items[$panelId] ?? [];
    }
}
