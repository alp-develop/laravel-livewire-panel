<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Cdn\CdnPluginResolver;
use PHPUnit\Framework\TestCase;

final class CdnPluginResolverTest extends TestCase
{
    private CdnPluginResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new CdnPluginResolver();
    }

    public function test_is_applicable_returns_true_when_routes_empty(): void
    {
        $this->assertTrue($this->resolver->isApplicable([], '/any-path'));
    }

    public function test_is_applicable_matches_exact_path(): void
    {
        $this->assertTrue($this->resolver->isApplicable(['dashboard'], '/dashboard'));
    }

    public function test_is_applicable_matches_wildcard(): void
    {
        $this->assertTrue($this->resolver->isApplicable(['admin/*'], '/admin/users'));
    }

    public function test_is_applicable_returns_false_for_non_matching(): void
    {
        $this->assertFalse($this->resolver->isApplicable(['admin/*'], '/public/home'));
    }

    public function test_is_applicable_strips_leading_slash(): void
    {
        $this->assertTrue($this->resolver->isApplicable(['/dashboard'], '/dashboard'));
    }

    public function test_resolve_assets_collects_css_and_js(): void
    {
        $config = [
            'cdn' => [
                [
                    'css' => ['a.css', 'b.css'],
                    'js' => ['a.js'],
                    'routes' => [],
                ],
            ],
        ];

        $result = $this->resolver->resolveAssets($config, '/');

        $this->assertSame(['a.css', 'b.css'], $result['css']);
        $this->assertSame(['a.js'], $result['js']);
    }

    public function test_resolve_assets_skips_non_matching_routes(): void
    {
        $config = [
            'cdn' => [
                [
                    'css' => ['a.css'],
                    'js' => ['a.js'],
                    'routes' => ['admin/*'],
                ],
            ],
        ];

        $result = $this->resolver->resolveAssets($config, '/public');

        $this->assertSame([], $result['css']);
        $this->assertSame([], $result['js']);
    }

    public function test_resolve_assets_returns_empty_without_cdn_key(): void
    {
        $result = $this->resolver->resolveAssets([], '/');

        $this->assertSame([], $result['css']);
        $this->assertSame([], $result['js']);
    }

    public function test_resolve_assets_handles_missing_css_or_js(): void
    {
        $config = [
            'cdn' => [
                [
                    'routes' => [],
                ],
            ],
        ];

        $result = $this->resolver->resolveAssets($config, '/');

        $this->assertSame([], $result['css']);
        $this->assertSame([], $result['js']);
    }

    public function test_resolve_assets_combines_multiple_plugins(): void
    {
        $config = [
            'cdn' => [
                ['css' => ['a.css'], 'js' => [], 'routes' => []],
                ['css' => ['b.css'], 'js' => ['b.js'], 'routes' => []],
            ],
        ];

        $result = $this->resolver->resolveAssets($config, '/');

        $this->assertSame(['a.css', 'b.css'], $result['css']);
        $this->assertSame(['b.js'], $result['js']);
    }
}
