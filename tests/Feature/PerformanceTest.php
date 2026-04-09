<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\PanelRenderer;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class PerformanceTest extends PanelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $reflection = new \ReflectionClass(PanelRenderer::class);
        $property   = $reflection->getProperty('cache');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    public function test_css_vars_memoization_returns_identical_result(): void
    {
        $first  = PanelRenderer::cssVars();
        $second = PanelRenderer::cssVars();
        $third  = PanelRenderer::cssVars();

        $this->assertSame($first, $second);
        $this->assertSame($second, $third);
    }

    public function test_layout_config_memoization_returns_identical_result(): void
    {
        $first  = PanelRenderer::layoutConfig();
        $second = PanelRenderer::layoutConfig();
        $third  = PanelRenderer::layoutConfig();

        $this->assertSame($first, $second);
        $this->assertSame($second, $third);
    }

    public function test_css_vars_cache_is_per_panel(): void
    {
        $context = $this->app->make(\AlpDevelop\LivewirePanel\PanelContext::class);

        $context->set('test');
        $first = PanelRenderer::cssVars();

        $reflection = new \ReflectionClass(PanelRenderer::class);
        $property   = $reflection->getProperty('cache');
        $property->setAccessible(true);
        $cache = $property->getValue(null);

        $this->assertArrayHasKey('cssVars', $cache);
        $this->assertArrayHasKey('test', $cache['cssVars']);
    }

    public function test_multiple_css_vars_calls_produce_same_output(): void
    {
        $results = [];
        for ($i = 0; $i < 5; $i++) {
            $results[] = PanelRenderer::cssVars();
        }

        for ($i = 1; $i < 5; $i++) {
            $this->assertSame($results[0], $results[$i]);
        }
    }

    public function test_theme_css_variables_are_deterministic(): void
    {
        $registry = $this->app->make(\AlpDevelop\LivewirePanel\Themes\ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');
        $config   = ['theming' => ['primary' => '#ff0000']];

        $first  = $theme->cssVariables($config);
        $second = $theme->cssVariables($config);

        $this->assertEquals($first, $second);
        $this->assertStringContainsString('#ff0000', $first);
    }

    public function test_css_vars_completes_within_time_budget(): void
    {
        $start = hrtime(true);
        for ($i = 0; $i < 100; $i++) {
            $reflection = new \ReflectionClass(PanelRenderer::class);
            $property   = $reflection->getProperty('cache');
            $property->setAccessible(true);
            $property->setValue(null, []);
            PanelRenderer::cssVars();
        }
        $elapsed = (hrtime(true) - $start) / 1e6;

        $this->assertLessThan(500, $elapsed, "100 cold cssVars() calls took {$elapsed}ms (budget: 500ms)");
    }

    public function test_theme_head_html_completes_within_time_budget(): void
    {
        $registry = $this->app->make(\AlpDevelop\LivewirePanel\Themes\ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');
        $config   = [
            'theming' => ['primary' => '#4f46e5'],
            'layout'  => ['dark_mode' => true],
        ];

        $start = hrtime(true);
        for ($i = 0; $i < 100; $i++) {
            $theme->headHtml($config);
        }
        $elapsed = (hrtime(true) - $start) / 1e6;

        $this->assertLessThan(200, $elapsed, "100 headHtml() calls took {$elapsed}ms (budget: 200ms)");
    }

    public function test_all_themes_head_html_within_time_budget(): void
    {
        $registry = $this->app->make(\AlpDevelop\LivewirePanel\Themes\ThemeRegistry::class);
        $config   = [
            'theming' => ['primary' => '#4f46e5'],
            'layout'  => ['dark_mode' => true],
        ];

        foreach (['bootstrap5', 'bootstrap4', 'tailwind'] as $themeId) {
            $theme = $registry->resolve($themeId);
            $start = hrtime(true);
            for ($i = 0; $i < 50; $i++) {
                $theme->headHtml($config);
            }
            $elapsed = (hrtime(true) - $start) / 1e6;
            $this->assertLessThan(200, $elapsed, "{$themeId} 50x headHtml() took {$elapsed}ms (budget: 200ms)");
        }
    }

    public function test_theme_registry_memory_with_100_themes(): void
    {
        $registry = new \AlpDevelop\LivewirePanel\Themes\ThemeRegistry();
        $start    = memory_get_usage(true);

        for ($i = 0; $i < 100; $i++) {
            $registry->register("theme_{$i}", \AlpDevelop\LivewirePanel\Themes\Bootstrap5Theme::class);
        }

        $diff = (memory_get_usage(true) - $start) / 1024;

        $this->assertLessThan(500, $diff, "100 theme registrations used {$diff}KB (budget: 500KB)");
    }

    public function test_module_registry_memory_with_100_modules(): void
    {
        $registry = new \AlpDevelop\LivewirePanel\Modules\ModuleRegistry();
        $start    = memory_get_usage(true);

        for ($i = 0; $i < 100; $i++) {
            $registry->register('admin', "App\\Modules\\Module{$i}");
        }

        $diff = (memory_get_usage(true) - $start) / 1024;

        $this->assertLessThan(500, $diff, "100 module registrations used {$diff}KB (budget: 500KB)");
    }

    public function test_widget_registry_memory_with_100_widgets(): void
    {
        $registry = new \AlpDevelop\LivewirePanel\Widgets\WidgetRegistry();
        $start    = memory_get_usage(true);

        for ($i = 0; $i < 100; $i++) {
            $registry->register("widget_{$i}", "App\\Widgets\\Widget{$i}");
        }

        $diff = (memory_get_usage(true) - $start) / 1024;

        $this->assertLessThan(500, $diff, "100 widget registrations used {$diff}KB (budget: 500KB)");
    }

    public function test_navigation_registry_memory_with_500_items(): void
    {
        $registry = new \AlpDevelop\LivewirePanel\Navigation\NavigationRegistry();
        $start    = memory_get_usage(true);

        for ($i = 0; $i < 500; $i++) {
            $registry->add('admin', new \AlpDevelop\LivewirePanel\Modules\NavigationItem(
                label: "Item {$i}",
                route: "panel.admin.item_{$i}",
                icon: 'fa-file',
            ));
        }

        $diff = (memory_get_usage(true) - $start) / 1024;

        $this->assertLessThan(1024, $diff, "500 navigation items used {$diff}KB (budget: 1024KB)");
    }
}
