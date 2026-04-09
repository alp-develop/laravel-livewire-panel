<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use PHPUnit\Framework\TestCase;

final class WidgetRegistryTest extends TestCase
{
    private WidgetRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new WidgetRegistry();
    }

    public function test_register_and_has(): void
    {
        $this->registry->register('stats-card', 'App\\Widgets\\StatsCard');

        $this->assertTrue($this->registry->has('stats-card'));
    }

    public function test_has_returns_false_for_unregistered(): void
    {
        $this->assertFalse($this->registry->has('nonexistent'));
    }

    public function test_all_returns_registered_widgets(): void
    {
        $this->registry->register('stats-card', 'App\\Widgets\\StatsCard');
        $this->registry->register('chart', 'App\\Widgets\\Chart');

        $all = $this->registry->all();

        $this->assertCount(2, $all);
        $this->assertSame('App\\Widgets\\StatsCard', $all['stats-card']);
        $this->assertSame('App\\Widgets\\Chart', $all['chart']);
    }

    public function test_all_returns_empty_initially(): void
    {
        $this->assertSame([], $this->registry->all());
    }

    public function test_register_overwrites_previous(): void
    {
        $this->registry->register('stats', 'App\\Widgets\\V1');
        $this->registry->register('stats', 'App\\Widgets\\V2');

        $this->assertSame('App\\Widgets\\V2', $this->registry->all()['stats']);
    }
}
