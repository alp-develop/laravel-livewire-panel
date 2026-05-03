<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Search;

use AlpDevelop\LivewirePanel\Auth\PanelGate;
use AlpDevelop\LivewirePanel\Modules\NavigationItem;
use AlpDevelop\LivewirePanel\Navigation\NavigationGroup;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use Illuminate\Support\Facades\Route;

final class NavigationSearchProvider implements SearchProviderInterface
{
    public function category(): string
    {
        return 'Pages';
    }

    public function icon(): string
    {
        return 'layer-group';
    }

    /** @return list<array<string, mixed>> */
    public function search(string $query, string $panelId): array
    {
        $navRegistry = app(NavigationRegistry::class);
        $gate        = app(PanelGate::class);
        $items       = $navRegistry->forPanel($panelId);
        $results     = [];

        foreach ($items as $item) {
            if ($item instanceof NavigationGroup) {
                $canSeeGroup = ($item->permission === '' || $item->permission === '0' || $gate->allows($item->permission))
                    && (in_array($item->roles, ['', '0', []], true) || $gate->hasRole($item->roles));

                if (!$canSeeGroup) {
                    continue;
                }

                foreach ($item->children as $child) {
                    $resolved = $this->resolveItem($child, $gate);
                    if ($resolved) {
                        $results[] = $resolved;
                    }
                }
            } else {
                $resolved = $this->resolveItem($item, $gate);
                if ($resolved) {
                    $results[] = $resolved;
                }
            }
        }

        if (trim($query) === '') {
            return $results;
        }

        $q = mb_strtolower(trim($query));

        $filtered = array_values(array_filter($results, fn(array $r) => str_contains(mb_strtolower((string) $r['label']), $q)
            || str_contains(mb_strtolower($r['description'] ?? ''), $q)));

        return array_slice($filtered, 0, 15);
    }

    /** @return array<string, mixed>|null */
    private function resolveItem(NavigationItem $item, PanelGate $gate): ?array
    {
        $canSee = ($item->permission === '' || $item->permission === '0' || $gate->allows($item->permission))
            && (in_array($item->roles, ['', '0', []], true) || $gate->hasRole($item->roles));

        if (!$canSee || !Route::has($item->route)) {
            return null;
        }

        return [
            'label'       => $item->label,
            'icon'        => $item->icon ?: 'layer-group',
            'url'         => route($item->route),
            'description' => $item->description,
        ];
    }
}
