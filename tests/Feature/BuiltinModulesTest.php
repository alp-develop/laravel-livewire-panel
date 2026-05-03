<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Dashboard\DashboardModule;
use AlpDevelop\LivewirePanel\Modules\Users\UsersModule;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class BuiltinModulesTest extends PanelTestCase
{
    private array $panelConfig = [
        'id'     => 'admin',
        'prefix' => 'admin',
        'guard'  => 'web',
    ];

    public function test_dashboard_module_id(): void
    {
        $module = new DashboardModule($this->panelConfig);
        $this->assertSame('dashboard', $module->id());
    }

    public function test_dashboard_module_navigation_items(): void
    {
        $module = new DashboardModule($this->panelConfig);
        $items  = $module->navigationItems();

        $this->assertCount(1, $items);
        $this->assertSame('panel::sidebar.dashboard', $items[0]->label);
        $this->assertSame('panel.admin.home', $items[0]->route);
        $this->assertSame('house', $items[0]->icon);
    }

    public function test_dashboard_module_permissions_empty(): void
    {
        $module = new DashboardModule($this->panelConfig);
        $this->assertSame([], $module->permissions());
    }

    public function test_users_module_id(): void
    {
        $module = new UsersModule($this->panelConfig);
        $this->assertSame('users', $module->id());
    }

    public function test_users_module_navigation_items(): void
    {
        $module = new UsersModule($this->panelConfig);
        $items  = $module->navigationItems();

        $this->assertCount(1, $items);
        $this->assertSame('panel::sidebar.users', $items[0]->label);
        $this->assertSame('panel.admin.users.index', $items[0]->route);
        $this->assertSame('users', $items[0]->icon);
    }

    public function test_users_module_permissions(): void
    {
        $module       = new UsersModule($this->panelConfig);
        $permissions  = $module->permissions();

        $this->assertContains('users.view', $permissions);
        $this->assertContains('users.create', $permissions);
        $this->assertContains('users.edit', $permissions);
        $this->assertContains('users.delete', $permissions);
    }
}
