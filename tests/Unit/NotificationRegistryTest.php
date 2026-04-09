<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Notifications\NotificationProviderInterface;
use AlpDevelop\LivewirePanel\Notifications\NotificationRegistry;
use PHPUnit\Framework\TestCase;

final class NotificationRegistryTest extends TestCase
{
    private NotificationRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new NotificationRegistry();
    }

    public function test_has_returns_false_initially(): void
    {
        $this->assertFalse($this->registry->has('admin'));
    }

    public function test_register_and_has(): void
    {
        $provider = $this->createMock(NotificationProviderInterface::class);
        $this->registry->register('admin', $provider);

        $this->assertTrue($this->registry->has('admin'));
    }

    public function test_resolve_returns_registered_provider(): void
    {
        $provider = $this->createMock(NotificationProviderInterface::class);
        $this->registry->register('admin', $provider);

        $this->assertSame($provider, $this->registry->resolve('admin'));
    }

    public function test_resolve_returns_null_for_unregistered(): void
    {
        $this->assertNull($this->registry->resolve('missing'));
    }

    public function test_register_overwrites_previous(): void
    {
        $first = $this->createMock(NotificationProviderInterface::class);
        $second = $this->createMock(NotificationProviderInterface::class);

        $this->registry->register('admin', $first);
        $this->registry->register('admin', $second);

        $this->assertSame($second, $this->registry->resolve('admin'));
    }
}
