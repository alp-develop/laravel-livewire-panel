<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Plugins\AbstractPlugin;
use PHPUnit\Framework\TestCase;

final class AbstractPluginTest extends TestCase
{
    private AbstractPlugin $plugin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->plugin = new class extends AbstractPlugin {
            public function id(): string { return 'test'; }
        };
    }

    public function test_before_boot_does_nothing_by_default(): void
    {
        $this->plugin->beforeBoot();

        $this->assertTrue(true);
    }

    public function test_after_boot_does_nothing_by_default(): void
    {
        $this->plugin->afterBoot();

        $this->assertTrue(true);
    }

    public function test_register_navigation_returns_empty_array(): void
    {
        $this->assertSame([], $this->plugin->registerNavigation());
    }

    public function test_register_widgets_returns_empty_array(): void
    {
        $this->assertSame([], $this->plugin->registerWidgets());
    }
}
