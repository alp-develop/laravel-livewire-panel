<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth;

use AlpDevelop\LivewirePanel\Auth\Drivers\LaravelGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\NullGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\SpatiGateDriver;
use AlpDevelop\LivewirePanel\PanelResolver;

final class PanelGate
{
    public function __construct(private readonly PanelResolver $resolver) {}

    public function allows(string $permission, mixed $user = null): bool
    {
        return $this->resolveDriver()->check($permission, $user);
    }

    public function denies(string $permission, mixed $user = null): bool
    {
        return !$this->allows($permission, $user);
    }

    public function hasRole(string|array $roles, mixed $user = null): bool
    {
        return $this->resolveDriver()->hasRole($roles, $user);
    }

    private function resolveDriver(): GateDriverInterface
    {
        $panelId    = $this->resolver->resolveFromRequest(request());
        $panelConfig = $this->resolver->resolveById($panelId);
        $gateConfig = $panelConfig['gate'] ?? null;

        return match ($gateConfig) {
            'laravel' => new LaravelGateDriver(),
            'spatie'  => new SpatiGateDriver(),
            default   => new NullGateDriver(),
        };
    }
}
