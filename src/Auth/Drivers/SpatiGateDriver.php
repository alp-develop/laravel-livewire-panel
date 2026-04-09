<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth\Drivers;

use AlpDevelop\LivewirePanel\Auth\GateDriverInterface;

final class SpatiGateDriver implements GateDriverInterface
{
    public function check(string $permission, mixed $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user === null) {
            return false;
        }

        return $user->hasPermissionTo($permission);
    }

    public function hasRole(string|array $roles, mixed $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user === null) {
            return false;
        }

        return is_array($roles)
            ? $user->hasAnyRole($roles)
            : $user->hasRole($roles);
    }
}
