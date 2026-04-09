<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Modules\NavigationItem;
use AlpDevelop\LivewirePanel\Navigation\NavigationGroup;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use PHPUnit\Framework\TestCase;

final class NavigationRegistryTest extends TestCase
{
    private NavigationRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new NavigationRegistry();
    }

    public function test_add_item_and_retrieve(): void
    {
        $item = new NavigationItem(label: 'Dashboard', route: 'panel.admin.home', icon: 'fa-home');
        $this->registry->add('admin', $item);

        $items = $this->registry->forPanel('admin');

        $this->assertCount(1, $items);
        $this->assertSame('Dashboard', $items[0]->label);
    }

    public function test_add_group_and_retrieve(): void
    {
        $child = new NavigationItem(label: 'List', route: 'panel.admin.users');
        $group = new NavigationGroup(label: 'Users', children: [$child], icon: 'fa-users');
        $this->registry->addGroup('admin', $group);

        $items = $this->registry->forPanel('admin');

        $this->assertCount(1, $items);
        $this->assertInstanceOf(NavigationGroup::class, $items[0]);
        $this->assertCount(1, $items[0]->children);
    }

    public function test_for_panel_returns_empty_for_unknown(): void
    {
        $this->assertSame([], $this->registry->forPanel('nonexistent'));
    }

    public function test_load_from_config_simple_items(): void
    {
        $config = [
            ['label' => 'Dashboard', 'route' => 'panel.admin.home', 'icon' => 'fa-home'],
            ['label' => 'Settings', 'route' => 'panel.admin.settings', 'icon' => 'fa-cog'],
        ];

        $this->registry->loadFromConfig('admin', $config);

        $items = $this->registry->forPanel('admin');

        $this->assertCount(2, $items);
        $this->assertInstanceOf(NavigationItem::class, $items[0]);
        $this->assertSame('Dashboard', $items[0]->label);
    }

    public function test_load_from_config_with_groups(): void
    {
        $config = [
            [
                'label'    => 'Users',
                'icon'     => 'fa-users',
                'children' => [
                    ['label' => 'List', 'route' => 'panel.admin.users'],
                    ['label' => 'Create', 'route' => 'panel.admin.users.create'],
                ],
            ],
        ];

        $this->registry->loadFromConfig('admin', $config);

        $items = $this->registry->forPanel('admin');

        $this->assertCount(1, $items);
        $this->assertInstanceOf(NavigationGroup::class, $items[0]);
        $this->assertCount(2, $items[0]->children);
    }

    public function test_items_from_different_panels_are_isolated(): void
    {
        $this->registry->add('admin', new NavigationItem(label: 'A', route: 'a'));
        $this->registry->add('operator', new NavigationItem(label: 'B', route: 'b'));

        $this->assertCount(1, $this->registry->forPanel('admin'));
        $this->assertCount(1, $this->registry->forPanel('operator'));
    }
}
