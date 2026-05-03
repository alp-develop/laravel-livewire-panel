<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\LoginComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\RegisterComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ForgotPasswordComponent;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\View\Livewire\Sidebar;
use AlpDevelop\LivewirePanel\View\Livewire\Navbar;

final class PanelHelpersTest extends PanelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PanelContext::class)->set('test');
    }

    public function test_panel_component_returns_login_by_default(): void
    {
        $result = panel_component('login');

        $this->assertSame(LoginComponent::class, $result);
    }

    public function test_panel_component_returns_register_by_default(): void
    {
        $result = panel_component('register');

        $this->assertSame(RegisterComponent::class, $result);
    }

    public function test_panel_component_returns_forgot_password_by_default(): void
    {
        $result = panel_component('forgot-password');

        $this->assertSame(ForgotPasswordComponent::class, $result);
    }

    public function test_panel_component_returns_sidebar_by_default(): void
    {
        $result = panel_component('sidebar');

        $this->assertSame(Sidebar::class, $result);
    }

    public function test_panel_component_returns_navbar_by_default(): void
    {
        $result = panel_component('navbar');

        $this->assertSame(Navbar::class, $result);
    }

    public function test_panel_component_returns_override_from_config(): void
    {
        $result = panel_component('login');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_panel_component_returns_empty_for_unknown_key(): void
    {
        $result = panel_component('nonexistent-component');

        $this->assertSame('', $result);
    }

    public function test_panel_route_helper_generates_named_route_url(): void
    {
        $result = panel_route('test', 'auth.login');

        $this->assertIsString($result);
        $this->assertStringContainsString('login', $result);
        $this->assertStringContainsString('test-panel', $result);
    }

    public function test_to_panel_helper_returns_portal_builder(): void
    {
        $builder = to_panel('test');

        $this->assertInstanceOf(\AlpDevelop\LivewirePanel\PanelPortalBuilder::class, $builder);
    }
}

final class PanelHelpersWithOverrideTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('laravel-livewire-panel.panels.test.components.login', RegisterComponent::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PanelContext::class)->set('test');
    }

    public function test_panel_component_returns_override_when_configured(): void
    {
        $result = panel_component('login');

        $this->assertSame(RegisterComponent::class, $result);
    }
}
