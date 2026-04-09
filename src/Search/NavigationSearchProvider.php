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

    public function search(string $query, string $panelId): array
    {
        $navRegistry = app(NavigationRegistry::class);
        $gate        = app(PanelGate::class);
        $items       = $navRegistry->forPanel($panelId);
        $results     = [];

        foreach ($items as $item) {
            if ($item instanceof NavigationGroup) {
                $canSeeGroup = (empty($item->permission) || $gate->allows($item->permission))
                    && (empty($item->roles) || $gate->hasRole($item->roles));

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

        return array_values(array_filter($results, function (array $r) use ($q) {
            return str_contains(mb_strtolower($r['label']), $q)
                || str_contains(mb_strtolower($r['description'] ?? ''), $q);
        }));
    }

    private function resolveItem(NavigationItem $item, PanelGate $gate): ?array
    {
        $canSee = (empty($item->permission) || $gate->allows($item->permission))
            && (empty($item->roles) || $gate->hasRole($item->roles));

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
