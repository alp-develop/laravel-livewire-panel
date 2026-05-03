<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Auth\Drivers\LaravelGateDriver;
use AlpDevelop\LivewirePanel\Auth\Drivers\NullGateDriver;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\TestCase;

final class GateDriverTest extends TestCase
{
    public function test_null_driver_check_always_returns_false(): void
    {
        $driver = new NullGateDriver();
        $this->assertFalse($driver->check('any-permission'));
        $this->assertFalse($driver->check('other', new \stdClass()));
    }

    public function test_null_driver_has_role_always_returns_false(): void
    {
        $driver = new NullGateDriver();
        $this->assertFalse($driver->hasRole('admin'));
        $this->assertFalse($driver->hasRole(['admin', 'editor']));
        $this->assertFalse($driver->hasRole('admin', new \stdClass()));
    }
}
