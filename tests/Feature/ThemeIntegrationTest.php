<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;

final class ThemeIntegrationTest extends PanelTestCase
{
    public function test_bootstrap5_head_html_contains_button_styles(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('bootstrap5');
        $html  = $theme->headHtml([]);

        $this->assertStringContainsString('.btn-primary', $html);
        $this->assertStringContainsString('--bs-btn-bg', $html);
    }

    public function test_bootstrap5_head_html_dark_mode_generates_css(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('bootstrap5');
        $html  = $theme->headHtml(['layout' => ['dark_mode' => true]]);

        $this->assertStringContainsString('[data-bs-theme=dark]', $html);
        $this->assertStringContainsString('--bs-body-bg', $html);
    }

    public function test_bootstrap5_head_html_sanitizes_malicious_values(): void
    {
        $theme  = $this->app->make(ThemeRegistry::class)->resolve('bootstrap5');
        $config = [
            'theming' => [
                'primary' => '#4f46e5;background:url("evil")',
            ],
            'layout' => ['dark_mode' => true],
        ];

        $html = $theme->headHtml($config);

        $this->assertStringNotContainsString(';background', $html);
        $this->assertStringNotContainsString('"evil"', $html);
    }

    public function test_bootstrap4_head_html_generates_css(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('bootstrap4');
        $html  = $theme->headHtml([]);

        $this->assertStringContainsString('<style>', $html);
    }

    public function test_bootstrap4_head_html_sanitizes_malicious_values(): void
    {
        $theme  = $this->app->make(ThemeRegistry::class)->resolve('bootstrap4');
        $config = [
            'theming' => [
                'font_family' => 'Inter;}</style><script>alert(1)</script>',
            ],
        ];

        $html = $theme->headHtml($config);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('}</style>', $html);
    }

    public function test_tailwind_head_html_generates_css(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('tailwind');
        $html  = $theme->headHtml([]);

        $this->assertStringContainsString('tailwind.config', $html);
    }

    public function test_tailwind_head_html_sanitizes_malicious_values(): void
    {
        $theme  = $this->app->make(ThemeRegistry::class)->resolve('tailwind');
        $config = [
            'theming' => [
                'primary' => '#4f46e5}<style>body{display:none}</style>',
            ],
        ];

        $html = $theme->headHtml($config);

        $this->assertStringNotContainsString('{display', $html);
        $this->assertStringNotContainsString('body{', $html);
    }

    public function test_all_themes_return_component_classes(): void
    {
        $registry = $this->app->make(ThemeRegistry::class);

        foreach (['bootstrap5', 'bootstrap4', 'tailwind'] as $id) {
            $theme   = $registry->resolve($id);
            $classes = $theme->componentClasses();

            $this->assertArrayHasKey('button', $classes, "Theme {$id} missing button classes");
            $this->assertArrayHasKey('alert', $classes, "Theme {$id} missing alert classes");
            $this->assertArrayHasKey('card', $classes, "Theme {$id} missing card classes");
        }
    }

    public function test_bootstrap4_generates_css_variables(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('bootstrap4');
        $vars  = $theme->cssVariables([]);

        $this->assertStringContainsString('--panel-primary', $vars);
        $this->assertStringContainsString('--panel-sidebar-bg', $vars);
    }

    public function test_tailwind_generates_css_variables(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('tailwind');
        $vars  = $theme->cssVariables([]);

        $this->assertStringContainsString('--panel-primary', $vars);
        $this->assertStringContainsString('--panel-sidebar-bg', $vars);
    }

    public function test_bootstrap4_dark_css_variables(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('bootstrap4');
        $vars  = $theme->darkCssVariables([]);

        $this->assertStringContainsString('--panel-primary', $vars);
        $this->assertStringContainsString('--panel-sidebar-bg', $vars);
    }

    public function test_tailwind_dark_css_variables(): void
    {
        $theme = $this->app->make(ThemeRegistry::class)->resolve('tailwind');
        $vars  = $theme->darkCssVariables([]);

        $this->assertStringContainsString('--panel-primary', $vars);
        $this->assertStringContainsString('--panel-sidebar-bg', $vars);
    }

    public function test_dark_css_variables_sanitize_malicious_values(): void
    {
        $theme  = $this->app->make(ThemeRegistry::class)->resolve('bootstrap5');
        $config = [
            'theming' => [
                'panel' => [
                    'dark' => [
                        'primary' => '#818cf8;background:url("evil")',
                    ],
                ],
            ],
        ];

        $vars = $theme->darkCssVariables($config);

        $this->assertStringNotContainsString(';background', $vars);
        $this->assertStringNotContainsString('"evil"', $vars);
    }

    public function test_content_max_width_is_included_when_set(): void
    {
        $theme  = $this->app->make(ThemeRegistry::class)->resolve('bootstrap5');
        $config = [
            'layout' => ['content_max_width' => '1400px'],
        ];

        $vars = $theme->cssVariables($config);

        $this->assertStringContainsString('--panel-content-max-width', $vars);
        $this->assertStringContainsString('1400px', $vars);
    }
}
