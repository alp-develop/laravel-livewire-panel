<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Themes\Bootstrap5Theme;
use PHPUnit\Framework\Attributes\DataProvider;

final class CssSanitizationFuzzTest extends PanelTestCase
{
    private Bootstrap5Theme $theme;

    protected function setUp(): void
    {
        parent::setUp();
        $this->theme = new Bootstrap5Theme();
    }

    public static function xssPayloadsProvider(): array
    {
        return [
            'script tag' => ['<script>alert(1)</script>'],
            'style tag' => ['</style><script>alert(1)</script>'],
            'expression' => ['expression(alert(1))'],
            'url javascript' => ['url(javascript:alert(1))'],
            'curly braces' => ['red} body{background:red'],
            'semicolon injection' => ['red; background-image: url(evil)'],
            'double quote' => ['red" onmouseover="alert(1)'],
            'single quote' => ["red' onmouseover='alert(1)"],
            'angle brackets' => ['<img src=x onerror=alert(1)>'],
            'null byte' => ["red\0"],
            'newline injection' => ["red\n} body { background: red"],
            'backslash escape' => ['red\\3c script\\3e'],
            'html entity' => ['red&lt;script&gt;alert(1)'],
            'import' => ['@import url(evil.css)'],
            'charset' => ['@charset "UTF-8"'],
            'behavior' => ['behavior:url(evil.htc)'],
            'moz binding' => ['-moz-binding:url(evil.xml#xss)'],
            'unicode escape' => ["\\75rl(javascript:alert(1))"],
        ];
    }

    #[DataProvider('xssPayloadsProvider')]
    public function test_sanitize_strips_dangerous_chars(string $payload): void
    {
        $reflection = new \ReflectionMethod($this->theme, 'sanitizeCssValue');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($this->theme, $payload);

        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
        $this->assertStringNotContainsString('{', $result);
        $this->assertStringNotContainsString('}', $result);
        $this->assertStringNotContainsString(';', $result);
        $this->assertStringNotContainsString('"', $result);
        $this->assertStringNotContainsString("'", $result);
    }

    #[DataProvider('xssPayloadsProvider')]
    public function test_head_html_does_not_contain_unescaped_payload(string $payload): void
    {
        $config = [
            'theming' => ['primary' => $payload],
            'layout'  => ['dark_mode' => true],
        ];

        $html = $this->theme->headHtml($config);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('expression(', $html);
    }

    #[DataProvider('xssPayloadsProvider')]
    public function test_css_variables_does_not_contain_unescaped_payload(string $payload): void
    {
        $config = [
            'theming' => ['primary' => $payload],
        ];

        $css = $this->theme->cssVariables($config);

        $this->assertStringNotContainsString('<script>', $css);
        $this->assertStringNotContainsString('javascript:', $css);
    }

    public function test_sanitize_preserves_valid_css_values(): void
    {
        $reflection = new \ReflectionMethod($this->theme, 'sanitizeCssValue');
        $reflection->setAccessible(true);

        $validValues = [
            '#4f46e5',
            '#fff',
            'rgb(79, 70, 229)',
            '14px',
            '0.9rem',
            'sans-serif',
            '260px',
            '8px',
            'calc(8px * 0.75)',
            'rgba(255,255,255,0.05)',
        ];

        foreach ($validValues as $value) {
            $result = $reflection->invoke($this->theme, $value);
            $this->assertNotEmpty($result, "Valid CSS value '{$value}' was emptied by sanitizer");
        }
    }
}
