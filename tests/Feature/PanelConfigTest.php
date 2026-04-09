<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\Config\PanelStyleConfig;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class PanelConfigTest extends PanelTestCase
{
    public function test_panel_config_returns_all_panels(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertIsArray($config->all());
        $this->assertNotEmpty($config->all());
    }

    public function test_panel_config_returns_ids(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertContains('test', $config->ids());
    }

    public function test_panel_config_returns_default_panel(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertEquals('test', $config->default());
    }

    public function test_panel_config_has_returns_true_for_existing(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertTrue($config->has('test'));
    }

    public function test_panel_config_has_returns_false_for_missing(): void
    {
        $config = $this->app->make(PanelConfig::class);

        $this->assertFalse($config->has('nonexistent'));
    }

    public function test_panel_config_get_returns_correct_data(): void
    {
        $config = $this->app->make(PanelConfig::class);
        $panel  = $config->get('test');

        $this->assertEquals('test', $panel['id']);
        $this->assertEquals('test-panel', $panel['prefix']);
        $this->assertEquals('web', $panel['guard']);
        $this->assertEquals('bootstrap5', $panel['theme']);
    }

    public function test_panel_config_normalizes_single_panel(): void
    {
        $config = new PanelConfig([
            'id'     => 'single',
            'prefix' => 'single',
            'theme'  => 'bootstrap5',
        ]);

        $this->assertTrue($config->has('single'));
        $this->assertEquals('single', $config->default());
    }

    public function test_style_config_can_be_resolved(): void
    {
        $styleConfig = $this->app->make(PanelStyleConfig::class);

        $this->assertInstanceOf(PanelStyleConfig::class, $styleConfig);
    }
}
