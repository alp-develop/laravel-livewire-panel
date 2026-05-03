<?php

declare(strict_types=1);

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\LoginComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\RegisterComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ForgotPasswordComponent;
use AlpDevelop\LivewirePanel\Modules\Errors\Http\Livewire\NotFoundComponent;
use AlpDevelop\LivewirePanel\View\Livewire\Sidebar;
use AlpDevelop\LivewirePanel\View\Livewire\Navbar;
use AlpDevelop\LivewirePanel\PanelPortalBuilder;
use AlpDevelop\LivewirePanel\PanelResolver;

if (!function_exists('panel_route')) {
    /** @param array<string, mixed> $parameters */
    function panel_route(string $panelId, string $routeName, array $parameters = []): string
    {
        return route("panel.{$panelId}.{$routeName}", $parameters);
    }
}

if (!function_exists('to_panel')) {
    function to_panel(string $panelId): PanelPortalBuilder
    {
        return new PanelPortalBuilder($panelId);
    }
}

if (!function_exists('panel_component')) {
    function panel_component(string $key): string
    {
        $defaults = [
            'login'           => LoginComponent::class,
            'register'        => RegisterComponent::class,
            'forgot-password' => ForgotPasswordComponent::class,
            'not-found'       => NotFoundComponent::class,
            'sidebar'         => Sidebar::class,
            'navbar'          => Navbar::class,
            'notifications'   => \AlpDevelop\LivewirePanel\View\Livewire\PanelNotifications::class,
        ];

        $resolver    = app(PanelResolver::class);
        $panelId     = $resolver->resolveFromRequest(request());
        $panelConfig = $resolver->resolveById($panelId);
        $components  = $panelConfig['components'] ?? [];

        return $components[$key] ?? $defaults[$key] ?? '';
    }
}
