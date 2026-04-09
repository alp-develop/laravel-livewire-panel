<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\PanelRenderer;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;

final class PanelRendererTest extends PanelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $reflection = new \ReflectionClass(PanelRenderer::class);
        $property   = $reflection->getProperty('cache');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    public function test_resolve_panel_id_returns_current_panel(): void
    {
        $panelId = PanelRenderer::resolvePanelId();

        $this->assertEquals('test', $panelId);
    }

    public function test_css_vars_returns_style_tag(): void
    {
        $output = PanelRenderer::cssVars();

        $this->assertStringStartsWith('<style>', $output);
        $this->assertStringEndsWith('</style>', $output);
        $this->assertStringContainsString('--panel-primary', $output);
    }

    public function test_css_vars_are_memoized(): void
    {
        $first  = PanelRenderer::cssVars();
        $second = PanelRenderer::cssVars();

        $this->assertSame($first, $second);
    }

    public function test_layout_config_returns_expected_keys(): void
    {
        $config = PanelRenderer::layoutConfig();

        $this->assertArrayHasKey('dark_mode', $config);
        $this->assertArrayHasKey('page_transition', $config);
        $this->assertArrayHasKey('back_to_top', $config);
        $this->assertArrayHasKey('show_search', $config);
        $this->assertArrayHasKey('show_notifications', $config);
    }

    public function test_layout_config_is_memoized(): void
    {
        $first  = PanelRenderer::layoutConfig();
        $second = PanelRenderer::layoutConfig();

        $this->assertSame($first, $second);
    }

    public function test_css_assets_returns_html_links(): void
    {
        $output = PanelRenderer::cssAssets('app');

        $this->assertStringContainsString('<link rel="stylesheet"', $output);
        $this->assertStringContainsString('font-awesome', $output);
    }

    public function test_js_assets_returns_script_tags(): void
    {
        $output = PanelRenderer::jsAssets('app');

        $this->assertStringContainsString('<script', $output);
    }

    public function test_asset_url_generates_correct_path(): void
    {
        $url = PanelRenderer::assetUrl('css/panel-base.css');

        $this->assertStringContainsString('/_panel/assets/css/panel-base.css', $url);
    }
}
