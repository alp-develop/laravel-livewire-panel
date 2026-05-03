<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\View\Livewire\Navbar;
use AlpDevelop\LivewirePanel\View\Livewire\Sidebar;
use Livewire\Livewire;

final class NavSidebarComponentsTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    public function test_navbar_renders(): void
    {
        Livewire::withoutLazyLoading()
            ->test(Navbar::class)
            ->assertStatus(200);
    }

    public function test_navbar_mount_sets_panel_id(): void
    {
        $component = Livewire::withoutLazyLoading()
            ->test(Navbar::class);

        $this->assertIsString($component->get('panelId'));
    }

    public function test_navbar_mount_accepts_title(): void
    {
        $component = Livewire::withoutLazyLoading()
            ->test(Navbar::class, ['title' => 'My Panel']);

        $this->assertSame('My Panel', $component->get('title'));
    }

    public function test_sidebar_renders(): void
    {
        Livewire::withoutLazyLoading()
            ->test(Sidebar::class)
            ->assertStatus(200);
    }

    public function test_sidebar_mount_sets_panel_id(): void
    {
        $component = Livewire::withoutLazyLoading()
            ->test(Sidebar::class);

        $this->assertIsString($component->get('panelId'));
    }
}
