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

    /** @param string|list<string> $roles */
    public function hasRole(string|array $roles, mixed $user = null): bool
    {
        $user ??= auth()->user();

        if (!is_object($user) || !method_exists($user, 'hasRole')) {
            return true;
        }

        if (is_array($roles) && method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($roles);
        }

        return $user->hasRole($roles);
    }
}
