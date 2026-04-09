<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth\Drivers;

use AlpDevelop\LivewirePanel\Auth\GateDriverInterface;
use Illuminate\Support\Facades\Gate;

final class LaravelGateDriver implements GateDriverInterface
{
    public function check(string $permission, mixed $user = null): bool
    {
        if ($user !== null) {
            return Gate::forUser($user)->allows($permission);
        }

        return Gate::allows($permission);
    }

    public function hasRole(string|array $roles, mixed $user = null): bool
    {
        $user = $user ?? auth()->user();

        if ($user === null) {
            return false;
        }

        if (!method_exists($user, 'hasRole')) {
            return true;
        }

        return is_array($roles)
            ? $user->hasAnyRole($roles)
            : $user->hasRole($roles);
    }
}
