<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Compat\LivewireVersion;
use PHPUnit\Framework\TestCase;

final class LivewireVersionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        LivewireVersion::reset();
    }

    public function test_major_returns_integer(): void
    {
        $major = LivewireVersion::major();

        $this->assertIsInt($major);
    }

    public function test_is_v3_returns_boolean(): void
    {
        $this->assertIsBool(LivewireVersion::isV3());
    }

    public function test_is_v4_or_above_returns_boolean(): void
    {
        $this->assertIsBool(LivewireVersion::isV4OrAbove());
    }

    public function test_major_caches_result(): void
    {
        $first = LivewireVersion::major();
        $second = LivewireVersion::major();

        $this->assertSame($first, $second);
    }

    public function test_reset_clears_cache(): void
    {
        LivewireVersion::major();
        LivewireVersion::reset();

        $ref = new \ReflectionClass(LivewireVersion::class);
        $prop = $ref->getProperty('major');
        $prop->setAccessible(true);

        $this->assertNull($prop->getValue());
    }

    public function test_is_v3_and_v4_are_exclusive_when_detected(): void
    {
        $major = LivewireVersion::major();

        if ($major === 3) {
            $this->assertTrue(LivewireVersion::isV3());
            $this->assertFalse(LivewireVersion::isV4OrAbove());
        } elseif ($major >= 4) {
            $this->assertFalse(LivewireVersion::isV3());
            $this->assertTrue(LivewireVersion::isV4OrAbove());
        } else {
            $this->assertFalse(LivewireVersion::isV3());
            $this->assertFalse(LivewireVersion::isV4OrAbove());
        }
    }
}
