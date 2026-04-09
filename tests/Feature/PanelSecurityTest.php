<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Auth\Drivers\LaravelGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\NullGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\SpatiGateDriver;
use AlpDevelop\LivewirePanel\Auth\PanelGate;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class PanelSecurityTest extends PanelTestCase
{
    public function test_null_gate_driver_denies_permission(): void
    {
        $driver = new NullGateDriver();

        $this->assertFalse($driver->check('anything'));
    }

    public function test_null_gate_driver_denies_role(): void
    {
        $driver = new NullGateDriver();

        $this->assertFalse($driver->hasRole('admin'));
    }

    public function test_null_gate_driver_denies_role_array(): void
    {
        $driver = new NullGateDriver();

        $this->assertFalse($driver->hasRole(['admin', 'editor']));
    }

    public function test_panel_context_starts_unresolved(): void
    {
        $context = new PanelContext();

        $this->assertFalse($context->resolved());
        $this->assertEquals('', $context->get());
    }

    public function test_panel_context_stores_panel_id(): void
    {
        $context = new PanelContext();
        $context->set('admin');

        $this->assertTrue($context->resolved());
        $this->assertEquals('admin', $context->get());
    }

    public function test_panel_resolver_returns_default_for_unknown_path(): void
    {
        $resolver = $this->app->make(PanelResolver::class);
        $request  = \Illuminate\Http\Request::create('/unknown-path', 'GET');

        $panelId = $resolver->resolveFromRequest($request);

        $this->assertEquals('test', $panelId);
    }

    public function test_panel_gate_denies_when_no_gate_configured(): void
    {
        $gate = $this->app->make(PanelGate::class);

        $this->assertTrue($gate->denies('any.permission'));
    }

    public function test_panel_config_throws_for_unknown_panel(): void
    {
        $this->expectException(\AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException::class);

        $config = $this->app->make(\AlpDevelop\LivewirePanel\Config\PanelConfig::class);
        $config->get('nonexistent-panel');
    }

    public function test_theme_registry_throws_for_unknown_theme(): void
    {
        $this->expectException(\AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException::class);

        $registry = $this->app->make(\AlpDevelop\LivewirePanel\Themes\ThemeRegistry::class);
        $registry->resolve('nonexistent-theme');
    }
}
