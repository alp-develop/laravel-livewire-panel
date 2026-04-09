<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth\Drivers;

use AlpDevelop\LivewirePanel\Auth\GateDriverInterface;

final class NullGateDriver implements GateDriverInterface
{
    public function check(string $permission, mixed $user = null): bool
    {
        return false;
    }

    public function hasRole(string|array $roles, mixed $user = null): bool
    {
        return false;
    }
}
