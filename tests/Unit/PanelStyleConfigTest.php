<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Config\PanelStyleConfig;
use AlpDevelop\LivewirePanel\Exceptions\PanelStyleNotFoundException;
use PHPUnit\Framework\TestCase;

final class PanelStyleConfigTest extends TestCase
{
    private PanelStyleConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new PanelStyleConfig();
    }

    public function test_has_returns_false_initially(): void
    {
        $this->assertFalse($this->config->has('style_table'));
    }

    public function test_all_returns_empty_initially(): void
    {
        $this->assertSame([], $this->config->all());
    }

    public function test_get_throws_when_not_found(): void
    {
        $this->expectException(PanelStyleNotFoundException::class);

        $this->config->get('nonexistent');
    }

    public function test_load_from_directory_loads_config_files(): void
    {
        $path = dirname(__DIR__, 2) . '/config/laravel-livewire-panel';
        $this->config->loadFromDirectory($path);

        $this->assertTrue($this->config->has('style_table'));
    }

    public function test_load_from_nonexistent_directory_does_nothing(): void
    {
        $this->config->loadFromDirectory('/nonexistent/path');

        $this->assertSame([], $this->config->all());
    }

    public function test_get_returns_loaded_style(): void
    {
        $path = dirname(__DIR__, 2) . '/config/laravel-livewire-panel';
        $this->config->loadFromDirectory($path);

        $style = $this->config->get('style_table');

        $this->assertIsArray($style);
        $this->assertSame('style_table', $style['id']);
    }
}
