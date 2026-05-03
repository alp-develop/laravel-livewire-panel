<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;
use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
use AlpDevelop\LivewirePanel\PanelKernel;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

final class PanelMiddlewareExtraTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('laravel-livewire-panel.panels.alt', [
            'id'     => 'alt',
            'prefix' => 'alt-panel',
            'guard'  => 'web',
            'theme'  => 'bootstrap5',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/migrations'
        );
    }

    public function test_middleware_redirects_to_target_panel_when_find_panel_matches(): void
    {
        $user = $this->makePanelUser();
        $this->actingAs($user, 'web');

        Route::get('/alt-panel', fn () => 'ok')->name('panel.alt.home');

        $registry = $this->app->make(PanelAccessRegistry::class);
        $registry->for('test', fn ($u) => false);
        $registry->for('alt', fn ($u) => true);

        $middleware = $this->app->make(PanelAuthMiddleware::class);
        $request    = Request::create('/test-panel/dashboard', 'GET');
        $request->setLaravelSession($this->app['session.store']);

        $response = $middleware->handle($request, fn () => response('ok'));

        $this->assertTrue($response->isRedirection());
    }

    public function test_kernel_accessors(): void
    {
        $kernel = $this->app->make(PanelKernel::class);

        $this->assertNotNull($kernel->config());
        $this->assertNotNull($kernel->themes());
        $this->assertNotNull($kernel->modules());
        $this->assertNotNull($kernel->plugins());
        $this->assertNotNull($kernel->widgets());
        $this->assertNotNull($kernel->styleConfig());
        $this->assertTrue($kernel->isBooted());
    }

    public function test_kernel_boot_sets_booted_flag(): void
    {
        $kernel = $this->app->make(PanelKernel::class);

        $kernel->boot();

        $this->assertTrue($kernel->isBooted());
    }

    public function test_kernel_boot_is_idempotent(): void
    {
        $kernel = $this->app->make(PanelKernel::class);

        $kernel->boot();
        $kernel->boot();

        $this->assertTrue($kernel->isBooted());
    }
}
