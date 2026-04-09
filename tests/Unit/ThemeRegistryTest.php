<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Exceptions\PanelNotFoundException;
use AlpDevelop\LivewirePanel\Themes\Bootstrap5Theme;
use AlpDevelop\LivewirePanel\Themes\TailwindTheme;
use AlpDevelop\LivewirePanel\Themes\ThemeInterface;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use PHPUnit\Framework\TestCase;

final class ThemeRegistryTest extends TestCase
{
    private ThemeRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new ThemeRegistry();
    }

    public function test_register_and_resolve(): void
    {
        $this->registry->register('bootstrap5', Bootstrap5Theme::class);

        $theme = $this->registry->resolve('bootstrap5');

        $this->assertInstanceOf(ThemeInterface::class, $theme);
        $this->assertInstanceOf(Bootstrap5Theme::class, $theme);
    }

    public function test_resolve_returns_same_instance(): void
    {
        $this->registry->register('bootstrap5', Bootstrap5Theme::class);

        $first  = $this->registry->resolve('bootstrap5');
        $second = $this->registry->resolve('bootstrap5');

        $this->assertSame($first, $second);
    }

    public function test_resolve_unknown_throws_exception(): void
    {
        $this->expectException(PanelNotFoundException::class);
        $this->expectExceptionMessage('Theme [unknown] is not registered.');

        $this->registry->resolve('unknown');
    }

    public function test_has_returns_true_for_registered(): void
    {
        $this->registry->register('tailwind', TailwindTheme::class);

        $this->assertTrue($this->registry->has('tailwind'));
    }

    public function test_has_returns_false_for_unregistered(): void
    {
        $this->assertFalse($this->registry->has('nonexistent'));
    }

    public function test_get_returns_class_string(): void
    {
        $this->registry->register('bootstrap5', Bootstrap5Theme::class);

        $this->assertSame(Bootstrap5Theme::class, $this->registry->get('bootstrap5'));
    }

    public function test_get_returns_empty_for_unregistered(): void
    {
        $this->assertSame('', $this->registry->get('nonexistent'));
    }

    public function test_all_returns_registered_themes(): void
    {
        $this->registry->register('bootstrap5', Bootstrap5Theme::class);
        $this->registry->register('tailwind', TailwindTheme::class);

        $all = $this->registry->all();

        $this->assertCount(2, $all);
        $this->assertSame(Bootstrap5Theme::class, $all['bootstrap5']);
        $this->assertSame(TailwindTheme::class, $all['tailwind']);
    }

    public function test_all_returns_empty_initially(): void
    {
        $this->assertSame([], $this->registry->all());
    }

    public function test_register_overwrites_previous(): void
    {
        $this->registry->register('theme', Bootstrap5Theme::class);
        $this->registry->register('theme', TailwindTheme::class);

        $this->assertSame(TailwindTheme::class, $this->registry->get('theme'));
    }
}
