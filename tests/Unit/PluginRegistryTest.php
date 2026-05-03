<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Plugins\AbstractPlugin;
use AlpDevelop\LivewirePanel\Plugins\PluginInterface;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use PHPUnit\Framework\TestCase;

final class TrackablePlugin extends AbstractPlugin
{
    public static bool $beforeBootCalled = false;
    public static bool $afterBootCalled  = false;

    public function id(): string
    {
        return 'trackable';
    }

    public function beforeBoot(): void
    {
        self::$beforeBootCalled = true;
    }

    public function afterBoot(): void
    {
        self::$afterBootCalled = true;
    }
}

final class PluginRegistryTest extends TestCase
{
    private PluginRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry                    = new PluginRegistry();
        TrackablePlugin::$beforeBootCalled = false;
        TrackablePlugin::$afterBootCalled  = false;
    }

    public function test_register_adds_class(): void
    {
        $this->registry->register('App\\Plugins\\Analytics');

        $this->assertSame(['App\\Plugins\\Analytics'], $this->registry->all());
    }

    public function test_all_returns_empty_initially(): void
    {
        $this->assertSame([], $this->registry->all());
    }

    public function test_all_instances_returns_empty_before_boot(): void
    {
        $this->registry->register('App\\Plugins\\Analytics');

        $this->assertSame([], $this->registry->allInstances());
    }

    public function test_register_multiple_preserves_order(): void
    {
        $this->registry->register('App\\Plugins\\A');
        $this->registry->register('App\\Plugins\\B');
        $this->registry->register('App\\Plugins\\C');

        $this->assertSame([
            'App\\Plugins\\A',
            'App\\Plugins\\B',
            'App\\Plugins\\C',
        ], $this->registry->all());
    }

    public function test_boot_instantiates_plugin_and_calls_before_boot(): void
    {
        $this->registry->register(TrackablePlugin::class);
        $this->registry->boot();

        $this->assertTrue(TrackablePlugin::$beforeBootCalled);
        $this->assertCount(1, $this->registry->allInstances());
        $this->assertInstanceOf(TrackablePlugin::class, $this->registry->allInstances()[0]);
    }

    public function test_after_boot_calls_plugin_after_boot(): void
    {
        $this->registry->register(TrackablePlugin::class);
        $this->registry->boot();
        $this->registry->afterBoot();

        $this->assertTrue(TrackablePlugin::$afterBootCalled);
    }

    public function test_after_boot_with_no_plugins_does_nothing(): void
    {
        $this->registry->afterBoot();
        $this->assertSame([], $this->registry->allInstances());
    }
}

