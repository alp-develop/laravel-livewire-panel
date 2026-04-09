<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;
use AlpDevelop\LivewirePanel\Events\PanelAccessDenied;
use AlpDevelop\LivewirePanel\Http\Middleware\PanelAuthMiddleware;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

final class PanelMiddlewareTest extends PanelTestCase
{
    public function test_middleware_redirects_unauthenticated_to_login(): void
    {
        $middleware = $this->app->make(PanelAuthMiddleware::class);
        $request    = Request::create('/test-panel/dashboard', 'GET');
        $request->setLaravelSession($this->app['session.store']);

        $response = $middleware->handle($request, fn () => response('ok'));

        $this->assertTrue($response->isRedirection());
    }

    public function test_middleware_allows_authenticated_user(): void
    {
        $this->actingAsPanelUser();

        $middleware = $this->app->make(PanelAuthMiddleware::class);
        $request    = Request::create('/test-panel/dashboard', 'GET');
        $request->setLaravelSession($this->app['session.store']);

        $response = $middleware->handle($request, fn () => response('ok'));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('ok', $response->getContent());
    }

    public function test_middleware_dispatches_access_denied_event(): void
    {
        Event::fake([PanelAccessDenied::class]);

        $user = $this->makePanelUser();
        $this->actingAs($user, 'web');

        $registry = $this->app->make(PanelAccessRegistry::class);
        $registry->for('test', fn ($u) => false);

        $middleware = $this->app->make(PanelAuthMiddleware::class);
        $request    = Request::create('/test-panel/dashboard', 'GET');
        $request->setLaravelSession($this->app['session.store']);

        $middleware->handle($request, fn () => response('ok'));

        Event::assertDispatched(PanelAccessDenied::class);
    }

    public function test_middleware_sets_panel_context(): void
    {
        $this->actingAsPanelUser();

        $middleware = $this->app->make(PanelAuthMiddleware::class);
        $request    = Request::create('/test-panel/dashboard', 'GET');
        $request->setLaravelSession($this->app['session.store']);

        $middleware->handle($request, fn () => response('ok'));

        $context = $this->app->make(\AlpDevelop\LivewirePanel\PanelContext::class);
        $this->assertTrue($context->resolved());
        $this->assertEquals('test', $context->get());
    }
}
