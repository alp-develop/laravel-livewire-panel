<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Http\Middleware\SetPanelLocale;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Http\Request;

final class SetPanelLocaleTest extends PanelTestCase
{
    public function test_sets_locale_from_session(): void
    {
        $middleware = new SetPanelLocale();
        $request = Request::create('/dashboard');
        $request->setLaravelSession($this->app['session.store']);
        $request->session()->put('panel_locale', 'es');

        $middleware->handle($request, fn () => response('ok'));

        $this->assertSame('es', app()->getLocale());
    }

    public function test_ignores_invalid_locale(): void
    {
        $originalLocale = app()->getLocale();
        $middleware = new SetPanelLocale();
        $request = Request::create('/dashboard');
        $request->setLaravelSession($this->app['session.store']);
        $request->session()->put('panel_locale', '123-invalid!!');

        $middleware->handle($request, fn () => response('ok'));

        $this->assertSame($originalLocale, app()->getLocale());
    }

    public function test_does_nothing_without_session_locale(): void
    {
        $originalLocale = app()->getLocale();
        $middleware = new SetPanelLocale();
        $request = Request::create('/dashboard');
        $request->setLaravelSession($this->app['session.store']);

        $middleware->handle($request, fn () => response('ok'));

        $this->assertSame($originalLocale, app()->getLocale());
    }

    public function test_passes_through_to_next(): void
    {
        $middleware = new SetPanelLocale();
        $request = Request::create('/dashboard');
        $request->setLaravelSession($this->app['session.store']);

        $response = $middleware->handle($request, fn () => response('passed'));

        $this->assertSame('passed', $response->getContent());
    }

    public function test_accepts_locale_with_region(): void
    {
        $middleware = new SetPanelLocale();
        $request = Request::create('/dashboard');
        $request->setLaravelSession($this->app['session.store']);
        $request->session()->put('panel_locale', 'pt_BR');

        $middleware->handle($request, fn () => response('ok'));

        $this->assertSame('pt_BR', app()->getLocale());
    }
}
