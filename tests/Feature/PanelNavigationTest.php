<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\PanelKernel;
use AlpDevelop\LivewirePanel\PanelResolver;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;

final class PanelNavigationTest extends PanelTestCase
{
    public function test_panel_config_can_be_resolved(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertInstanceOf(PanelConfig::class, $config);
    }

    public function test_panel_config_has_test_panel(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertTrue($config->has('test'));
    }

    public function test_panel_config_returns_panel_data(): void
    {
        $config = $this->app->make(PanelConfig::class);
        $panel  = $config->get('test');

        $this->assertArrayHasKey('id', $panel);
        $this->assertArrayHasKey('prefix', $panel);
        $this->assertArrayHasKey('theme', $panel);
        $this->assertEquals('test', $panel['id']);
        $this->assertEquals('test-panel', $panel['prefix']);
    }

    public function test_theme_registry_has_bootstrap5(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');

        $this->assertNotNull($theme);
        $this->assertEquals('bootstrap5', $theme->id());
    }

    public function test_theme_registry_has_bootstrap4(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap4');

        $this->assertEquals('bootstrap4', $theme->id());
    }

    public function test_theme_registry_has_tailwind(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('tailwind');

        $this->assertEquals('tailwind', $theme->id());
    }

    public function test_panel_kernel_is_singleton(): void
    {
        $a = $this->app->make(PanelKernel::class);
        $b = $this->app->make(PanelKernel::class);

        $this->assertSame($a, $b);
    }

    public function test_panel_resolver_resolves_from_path(): void
    {
        $resolver = $this->app->make(PanelResolver::class);
        $request  = \Illuminate\Http\Request::create('/test-panel/dashboard', 'GET');

        $panelId = $resolver->resolveFromRequest($request);

        $this->assertEquals('test', $panelId);
    }
}
