<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Compat;

use Illuminate\Support\Facades\Route;

final class LivewireCompat
{
    public static function registerComponent(string $name, string $class): void
    {
        \Livewire\Livewire::component($name, $class);
    }

    public static function pageRoute(string $uri, string $component): \Illuminate\Routing\Route
    {
        if (LivewireVersion::isV4OrAbove() && method_exists(Route::class, 'livewire')) {
            return Route::livewire($uri, $component);
        }

        return Route::get($uri, $component);
    }

    public static function supportsDefer(): bool
    {
        return LivewireVersion::isV4OrAbove();
    }

    public static function supportsIslands(): bool
    {
        return LivewireVersion::isV4OrAbove();
    }

    public static function supportsAsyncActions(): bool
    {
        return LivewireVersion::isV4OrAbove();
    }
}
