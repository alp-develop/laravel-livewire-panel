<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Dashboard\Http\Livewire\DashboardPage;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Livewire\Livewire;

final class DashboardPageTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    public function test_dashboard_renders_without_stats(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(DashboardPage::class)
            ->assertStatus(200);
    }

    public function test_dashboard_renders_with_configured_stats(): void
    {
        $this->app['config']->set('laravel-livewire-panel.panels.test.dashboard_stats', [
            ['title' => 'Total Users', 'value' => '100'],
            ['title' => 'Revenue', 'value' => '$5,000'],
        ]);

        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(DashboardPage::class)
            ->assertStatus(200);
    }

    public function test_dashboard_uses_panel_context(): void
    {
        $context = $this->app->make(PanelContext::class);
        $context->set('test');

        $this->assertTrue($context->resolved());
        $this->assertSame('test', $context->get());
    }
}
