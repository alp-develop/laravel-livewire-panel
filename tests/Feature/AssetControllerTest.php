<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class AssetControllerTest extends PanelTestCase
{
    protected function defineRoutes($router): void
    {
        $router->get('/panel-assets/{file}', \AlpDevelop\LivewirePanel\Http\Controllers\AssetController::class)
            ->where('file', '.*')
            ->name('panel.assets');
    }

    public function test_serves_css_file_with_correct_content_type(): void
    {
        $response = $this->get('/panel-assets/css/panel-base.css');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/css', $response->headers->get('Content-Type'));
    }

    public function test_serves_js_file_with_correct_content_type(): void
    {
        $response = $this->get('/panel-assets/js/panel-init.js');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/javascript', $response->headers->get('Content-Type'));
    }

    public function test_returns_404_for_nonexistent_file(): void
    {
        $response = $this->get('/panel-assets/css/nonexistent.css');

        $response->assertStatus(404);
    }

    public function test_blocks_path_traversal(): void
    {
        $response = $this->get('/panel-assets/../../composer.json');

        $response->assertStatus(404);
    }

    public function test_sets_cache_header(): void
    {
        $response = $this->get('/panel-assets/css/panel-base.css');

        $response->assertStatus(200);
        $this->assertStringContainsString('max-age=604800', $response->headers->get('Cache-Control'));
    }
}
