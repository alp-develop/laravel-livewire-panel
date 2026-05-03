<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\PanelPortalBuilder;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class PanelPortalBuilderTest extends PanelTestCase
{
    public function test_route_generates_url_string(): void
    {
        $builder = new PanelPortalBuilder('test');
        $result  = $builder->route('auth.login');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_route_url_contains_panel_prefix(): void
    {
        $builder = new PanelPortalBuilder('test');
        $result  = $builder->route('auth.login');

        $this->assertStringContainsString('test-panel', $result);
    }

    public function test_route_url_contains_route_segment(): void
    {
        $builder = new PanelPortalBuilder('test');
        $result  = $builder->route('auth.login');

        $this->assertStringContainsString('login', $result);
    }

    public function test_route_accepts_parameters(): void
    {
        $builder = new PanelPortalBuilder('test');
        $result  = $builder->route('auth.reset-password', ['token' => 'abc123']);

        $this->assertIsString($result);
        $this->assertStringContainsString('abc123', $result);
    }

    public function test_home_returns_same_as_route_home(): void
    {
        $this->app['router']->get('/test-panel', fn () => 'ok')->name('panel.test.home');

        $builder = new PanelPortalBuilder('test');

        $this->assertSame($builder->route('home'), $builder->home());
    }

    public function test_home_url_contains_panel_prefix(): void
    {
        $this->app['router']->get('/test-panel', fn () => 'ok')->name('panel.test.home');

        $builder = new PanelPortalBuilder('test');

        $this->assertStringContainsString('test-panel', $builder->home());
    }

    public function test_to_panel_helper_returns_builder(): void
    {
        $result = to_panel('test');

        $this->assertInstanceOf(PanelPortalBuilder::class, $result);
    }

    public function test_to_panel_helper_generates_same_as_builder(): void
    {
        $direct = new PanelPortalBuilder('test');
        $helper = to_panel('test');

        $this->assertSame($direct->route('auth.login'), $helper->route('auth.login'));
    }
}
