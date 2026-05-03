<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class PanelAuthMiddlewareTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function defineRoutes($router): void
    {
        $router->get('/test-panel/home', function () {
            return response('ok');
        })
            ->middleware(['web', PanelAuthMiddleware::class])
            ->name('panel.test.home');

        $router->get('/test-panel/login', function () {
            return response('login');
        })->name('panel.test.auth.login');
    }

    public function test_middleware_redirects_unauthenticated_to_login(): void
    {
        $response = $this->get('/test-panel/home');

        $response->assertRedirect('/test-panel/login');
    }

    public function test_middleware_sets_panel_context(): void
    {
        $user = $this->makePanelUser();
        $this->actingAs($user);

        $this->get('/test-panel/home');

        $context = $this->app->make(PanelContext::class);
        $this->assertTrue($context->resolved());
        $this->assertSame('test', $context->get());
    }

    public function test_middleware_allows_authenticated_user(): void
    {
        $user = $this->makePanelUser();
        $this->actingAs($user);

        $response = $this->get('/test-panel/home');

        $response->assertOk();
    }
}
