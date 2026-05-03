<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Auth;

use AlpDevelop\LivewirePanel\Auth\Drivers\LaravelGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\NullGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\SpatiGateDriver;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;

/**
 * Resolves and caches gate driver instances per panel.
 *
 * Supports Spatie Permission, Laravel Gate, and custom drivers via `GateDriverInterface`.
 * Bound as `scoped()` in the service provider for Octane compatibility.
 */
final class PanelGate
{
    /** @var array<string, GateDriverInterface> */
    private array $drivers = [];

    public function __construct(
        private readonly PanelResolver $resolver,
        private readonly PanelContext  $context,
    ) {}

    public function allows(string $permission, mixed $user = null): bool
    {
        return $this->resolveDriver()->check($permission, $user);
    }

    public function denies(string $permission, mixed $user = null): bool
    {
        return !$this->allows($permission, $user);
    }

    /** @param string|list<string> $roles */
    public function hasRole(string|array $roles, mixed $user = null): bool
    {
        return $this->resolveDriver()->hasRole($roles, $user);
    }

    private function resolveDriver(): GateDriverInterface
    {
        $panelId = $this->context->resolved()
            ? $this->context->get()
            : $this->resolver->resolveFromRequest(request());

        if (isset($this->drivers[$panelId])) {
            return $this->drivers[$panelId];
        }

        $panelConfig = $this->resolver->resolveById($panelId);
        $gateConfig  = $panelConfig['gate'] ?? null;

        return $this->drivers[$panelId] = match ($gateConfig) {
            'laravel' => new LaravelGateDriver(),
            'spatie'  => new SpatiGateDriver(),
            default   => new NullGateDriver(),
        };
    }
}
