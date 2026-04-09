<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Compat\LivewireCompat;
use AlpDevelop\LivewirePanel\Compat\LivewireVersion;
use PHPUnit\Framework\TestCase;

final class LivewireCompatTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        LivewireVersion::reset();
    }

    public function test_supports_defer_depends_on_version(): void
    {
        $result = LivewireCompat::supportsDefer();

        if (LivewireVersion::major() >= 4) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function test_supports_islands_depends_on_version(): void
    {
        $result = LivewireCompat::supportsIslands();

        if (LivewireVersion::major() >= 4) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }

    public function test_supports_async_actions_depends_on_version(): void
    {
        $result = LivewireCompat::supportsAsyncActions();

        if (LivewireVersion::major() >= 4) {
            $this->assertTrue($result);
        } else {
            $this->assertFalse($result);
        }
    }
}
