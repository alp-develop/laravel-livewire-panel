<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Themes\AbstractTheme;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;

final class PanelThemeTest extends PanelTestCase
{
    public function test_bootstrap5_generates_css_variables(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');
        $vars     = $theme->cssVariables([]);

        $this->assertStringContainsString('--panel-primary', $vars);
        $this->assertStringContainsString('--panel-sidebar-bg', $vars);
        $this->assertStringContainsString('--panel-navbar-bg', $vars);
    }

    public function test_bootstrap5_generates_dark_css_variables(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');
        $vars     = $theme->darkCssVariables([]);

        $this->assertStringContainsString('--panel-primary', $vars);
        $this->assertStringContainsString('--panel-sidebar-bg', $vars);
    }

    public function test_css_variables_sanitize_malicious_values(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');

        $maliciousConfig = [
            'theming' => [
                'primary'   => '#4f46e5;background:url("evil")',
                'secondary' => '#6c757d}<style>a{color:red}</style>',
            ],
        ];

        $vars = $theme->cssVariables($maliciousConfig);

        $this->assertStringNotContainsString(';background', $vars, 'Semicolon injection should be stripped');
        $this->assertStringNotContainsString('<style>', $vars, 'HTML tags should be stripped');
        $this->assertStringNotContainsString('{color', $vars, 'Curly braces should be stripped');
        $this->assertStringNotContainsString('"evil"', $vars, 'Quotes should be stripped');
    }

    public function test_theme_returns_css_assets(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');

        $this->assertIsArray($theme->cssAssets());
        $this->assertNotEmpty($theme->cssAssets());
    }

    public function test_theme_returns_js_assets(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');

        $this->assertIsArray($theme->jsAssets());
    }

    public function test_theme_has_registered_components(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $theme    = $registry->resolve('bootstrap5');

        $this->assertEquals('bootstrap5', $theme->id());
    }

    public function test_all_themes_implement_interface(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);

        foreach (['bootstrap5', 'bootstrap4', 'tailwind'] as $id) {
            $theme = $registry->resolve($id);
            $this->assertInstanceOf(\AlpDevelop\LivewirePanel\Themes\ThemeInterface::class, $theme);
            $this->assertEquals($id, $theme->id());
        }
    }

    public function test_theme_registry_has_check(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);

        $this->assertTrue($registry->has('bootstrap5'));
        $this->assertFalse($registry->has('nonexistent'));
    }
}
