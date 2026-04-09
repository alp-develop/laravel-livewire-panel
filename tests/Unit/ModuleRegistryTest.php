<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use PHPUnit\Framework\TestCase;

final class ModuleRegistryTest extends TestCase
{
    private ModuleRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new ModuleRegistry();
    }

    public function test_register_and_retrieve_for_panel(): void
    {
        $this->registry->register('admin', 'App\\Modules\\Dashboard');

        $modules = $this->registry->forPanel('admin');

        $this->assertCount(1, $modules);
        $this->assertSame('App\\Modules\\Dashboard', $modules[0]);
    }

    public function test_for_panel_returns_empty_for_unknown(): void
    {
        $this->assertSame([], $this->registry->forPanel('nonexistent'));
    }

    public function test_register_multiple_modules_per_panel(): void
    {
        $this->registry->register('admin', 'App\\Modules\\Dashboard');
        $this->registry->register('admin', 'App\\Modules\\Users');
        $this->registry->register('admin', 'App\\Modules\\Settings');

        $modules = $this->registry->forPanel('admin');

        $this->assertCount(3, $modules);
    }

    public function test_register_modules_for_different_panels(): void
    {
        $this->registry->register('admin', 'App\\Modules\\Dashboard');
        $this->registry->register('operator', 'App\\Modules\\OperatorHome');

        $this->assertCount(1, $this->registry->forPanel('admin'));
        $this->assertCount(1, $this->registry->forPanel('operator'));
    }

    public function test_all_returns_all_panels(): void
    {
        $this->registry->register('admin', 'App\\Modules\\A');
        $this->registry->register('operator', 'App\\Modules\\B');

        $all = $this->registry->all();

        $this->assertArrayHasKey('admin', $all);
        $this->assertArrayHasKey('operator', $all);
    }

    public function test_all_returns_empty_initially(): void
    {
        $this->assertSame([], $this->registry->all());
    }
}
