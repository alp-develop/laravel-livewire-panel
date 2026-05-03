<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\View\Components\Portal;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class ViewComponentsTest extends PanelTestCase
{
    public function test_portal_default_route_is_home(): void
    {
        $portal = new Portal(panel: 'admin');
        $this->assertSame('home', $portal->route);
    }

    public function test_portal_stores_panel(): void
    {
        $portal = new Portal(panel: 'my-panel', route: 'dashboard');
        $this->assertSame('my-panel', $portal->panel);
        $this->assertSame('dashboard', $portal->route);
    }

    public function test_portal_stores_params(): void
    {
        $portal = new Portal(panel: 'admin', route: 'user', params: ['id' => 5]);
        $this->assertSame(['id' => 5], $portal->params);
    }
}
