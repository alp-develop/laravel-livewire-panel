<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Plugins\PluginInterface;
use AlpDevelop\LivewirePanel\Plugins\PluginRegistry;
use PHPUnit\Framework\TestCase;

final class PluginRegistryTest extends TestCase
{
    private PluginRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new PluginRegistry();
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
}
