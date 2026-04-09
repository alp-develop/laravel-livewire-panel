<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Foundation\Auth\User;

final class PanelAuthTest extends PanelTestCase
{
    public function test_panel_access_registry_can_be_resolved(): void
    {
        $registry = $this->app->make(PanelAccessRegistry::class);

        $this->assertInstanceOf(PanelAccessRegistry::class, $registry);
    }

    public function test_panel_access_allows_user_when_no_gate_defined(): void
    {
        $registry = $this->app->make(PanelAccessRegistry::class);

        $user = $this->makePanelUser();

        $this->assertFalse($registry->has('test'));
    }

    public function test_panel_access_allows_user_when_gate_returns_true(): void
    {
        $registry = $this->app->make(PanelAccessRegistry::class);
        $registry->for('test', fn (User $user) => $user->email === 'test@example.com');

        $user   = $this->makePanelUser(['email' => 'test@example.com']);
        $result = $registry->check('test', $user);

        $this->assertTrue($result);
    }

    public function test_panel_access_denies_user_when_gate_returns_false(): void
    {
        $registry = $this->app->make(PanelAccessRegistry::class);
        $registry->for('test', fn (User $user) => $user->email === 'admin@panel.test');

        $user   = $this->makePanelUser(['email' => 'other@example.com']);
        $result = $registry->check('test', $user);

        $this->assertFalse($result);
    }

    public function test_panel_access_registry_is_singleton(): void
    {
        $a = $this->app->make(PanelAccessRegistry::class);
        $b = $this->app->make(PanelAccessRegistry::class);

        $this->assertSame($a, $b);
    }
}
