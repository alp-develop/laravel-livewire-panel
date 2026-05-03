<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth;

interface GateDriverInterface
{
    public function check(string $permission, mixed $user = null): bool;

    /** @param string|list<string> $roles */
    public function hasRole(string|array $roles, mixed $user = null): bool;
}
