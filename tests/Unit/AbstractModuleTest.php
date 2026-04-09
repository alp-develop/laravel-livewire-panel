<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Modules\AbstractModule;
use PHPUnit\Framework\TestCase;

final class AbstractModuleTest extends TestCase
{
    public function test_panel_id_returns_config_id(): void
    {
        $module = $this->makeModule(['id' => 'admin', 'prefix' => 'admin', 'guard' => 'web']);

        $ref = new \ReflectionMethod($module, 'panelId');
        $ref->setAccessible(true);

        $this->assertSame('admin', $ref->invoke($module));
    }

    public function test_panel_id_defaults_to_default(): void
    {
        $module = $this->makeModule([]);

        $ref = new \ReflectionMethod($module, 'panelId');
        $ref->setAccessible(true);

        $this->assertSame('default', $ref->invoke($module));
    }

    public function test_prefix_returns_trimmed_value(): void
    {
        $module = $this->makeModule(['prefix' => '/admin/']);

        $ref = new \ReflectionMethod($module, 'prefix');
        $ref->setAccessible(true);

        $this->assertSame('admin', $ref->invoke($module));
    }

    public function test_prefix_handles_empty(): void
    {
        $module = $this->makeModule([]);

        $ref = new \ReflectionMethod($module, 'prefix');
        $ref->setAccessible(true);

        $this->assertSame('', $ref->invoke($module));
    }

    public function test_guard_returns_config_value(): void
    {
        $module = $this->makeModule(['guard' => 'admin']);

        $ref = new \ReflectionMethod($module, 'guard');
        $ref->setAccessible(true);

        $this->assertSame('admin', $ref->invoke($module));
    }

    public function test_guard_defaults_to_web(): void
    {
        $module = $this->makeModule([]);

        $ref = new \ReflectionMethod($module, 'guard');
        $ref->setAccessible(true);

        $this->assertSame('web', $ref->invoke($module));
    }

    public function test_permissions_returns_empty_by_default(): void
    {
        $module = $this->makeModule([]);

        $this->assertSame([], $module->permissions());
    }

    public function test_navigation_items_returns_empty_by_default(): void
    {
        $module = $this->makeModule([]);

        $this->assertSame([], $module->navigationItems());
    }

    public function test_user_menu_items_returns_empty_by_default(): void
    {
        $module = $this->makeModule([]);

        $this->assertSame([], $module->userMenuItems());
    }

    private function makeModule(array $config): AbstractModule
    {
        return new class($config) extends AbstractModule {
            public function id(): string { return 'test'; }
            public function routes(): void {}
        };
    }
}
