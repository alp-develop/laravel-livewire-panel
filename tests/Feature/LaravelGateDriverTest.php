<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Auth\Drivers\LaravelGateDriver;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;

final class LaravelGateDriverTest extends PanelTestCase
{
    public function test_check_returns_false_when_gate_denies(): void
    {
        Gate::define('edit-posts', fn() => false);

        $driver = new LaravelGateDriver();
        $this->assertFalse($driver->check('edit-posts'));
    }

    public function test_check_returns_true_when_gate_allows(): void
    {
        $user = new User();
        $user->id = 1;
        $this->actingAs($user);

        Gate::define('view-dashboard', fn() => true);

        $driver = new LaravelGateDriver();
        $this->assertTrue($driver->check('view-dashboard'));
    }

    public function test_check_for_specific_user(): void
    {
        Gate::define('do-something', fn($user) => $user instanceof User);

        $user   = new User();
        $driver = new LaravelGateDriver();

        $this->assertTrue($driver->check('do-something', $user));
    }

    public function test_has_role_returns_true_when_user_has_no_hasRole_method(): void
    {
        $driver = new LaravelGateDriver();
        $this->assertTrue($driver->hasRole('admin', new \stdClass()));
    }

    public function test_has_role_returns_true_when_no_user(): void
    {
        $driver = new LaravelGateDriver();
        $this->assertTrue($driver->hasRole('admin'));
    }

    public function test_has_role_with_array_when_user_has_no_method(): void
    {
        $driver = new LaravelGateDriver();
        $this->assertTrue($driver->hasRole(['admin', 'editor'], new \stdClass()));
    }
}
