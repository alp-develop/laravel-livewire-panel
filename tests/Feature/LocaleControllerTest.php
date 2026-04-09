<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class LocaleControllerTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function defineRoutes($router): void
    {
        $router->post('/panel/locale/{locale}', \AlpDevelop\LivewirePanel\Http\Controllers\LocaleController::class)
            ->middleware('web')
            ->name('panel.locale');
    }

    public function test_sets_locale_in_session(): void
    {
        $response = $this->post('/panel/locale/fr');

        $response->assertRedirect();
        $this->assertSame('fr', session('panel_locale'));
    }

    public function test_rejects_invalid_locale(): void
    {
        session()->put('panel_locale', 'en');

        $response = $this->post('/panel/locale/<script>');

        $response->assertRedirect();
        $this->assertSame('en', session('panel_locale'));
    }

    public function test_accepts_locale_with_region(): void
    {
        $response = $this->post('/panel/locale/pt_BR');

        $response->assertRedirect();
        $this->assertSame('pt_BR', session('panel_locale'));
    }

    public function test_sets_app_locale(): void
    {
        $this->post('/panel/locale/ja');

        $this->assertSame('ja', app()->getLocale());
    }

    public function test_accepts_locale_with_dash(): void
    {
        $response = $this->post('/panel/locale/zh-CN');

        $response->assertRedirect();
        $this->assertSame('zh-CN', session('panel_locale'));
    }
}
