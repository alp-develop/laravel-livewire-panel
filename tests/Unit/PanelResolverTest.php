<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Config\PanelConfig;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

final class PanelResolverTest extends TestCase
{
    private function makeResolver(array $config): PanelResolver
    {
        return new PanelResolver(new PanelConfig($config));
    }

    public function test_resolve_by_prefix(): void
    {
        $resolver = $this->makeResolver([
            'panels' => [
                'admin' => ['prefix' => 'admin'],
            ],
        ]);

        $request = Request::create('/admin/dashboard', 'GET');
        $this->assertSame('admin', $resolver->resolveFromRequest($request));
    }

    public function test_resolve_exact_prefix_match(): void
    {
        $resolver = $this->makeResolver([
            'panels' => [
                'admin' => ['prefix' => 'admin'],
            ],
        ]);

        $request = Request::create('/admin', 'GET');
        $this->assertSame('admin', $resolver->resolveFromRequest($request));
    }

    public function test_resolve_root_panel_as_fallback(): void
    {
        $resolver = $this->makeResolver([
            'default' => 'app',
            'panels'  => [
                'app' => ['prefix' => ''],
            ],
        ]);

        $request = Request::create('/any-path', 'GET');
        $this->assertSame('app', $resolver->resolveFromRequest($request));
    }

    public function test_resolve_falls_back_to_default_when_no_match(): void
    {
        $resolver = $this->makeResolver([
            'default' => 'admin',
            'panels'  => [
                'admin' => ['prefix' => 'admin'],
            ],
        ]);

        $request = Request::create('/other', 'GET');
        $this->assertSame('admin', $resolver->resolveFromRequest($request));
    }

    public function test_has_panel_returns_true_for_existing(): void
    {
        $resolver = $this->makeResolver([
            'panels' => ['admin' => ['prefix' => 'admin']],
        ]);

        $this->assertTrue($resolver->hasPanel('admin'));
    }

    public function test_has_panel_returns_false_for_missing(): void
    {
        $resolver = $this->makeResolver([
            'panels' => ['admin' => ['prefix' => 'admin']],
        ]);

        $this->assertFalse($resolver->hasPanel('nonexistent'));
    }

    public function test_resolve_by_id_returns_panel_config(): void
    {
        $resolver = $this->makeResolver([
            'panels' => [
                'admin' => ['prefix' => 'admin', 'theme' => 'bootstrap5'],
            ],
        ]);

        $config = $resolver->resolveById('admin');
        $this->assertSame('admin', $config['prefix']);
    }

    public function test_prefers_prefixed_panel_over_root(): void
    {
        $resolver = $this->makeResolver([
            'panels' => [
                'root'  => ['prefix' => ''],
                'admin' => ['prefix' => 'admin'],
            ],
        ]);

        $request = Request::create('/admin/users', 'GET');
        $this->assertSame('admin', $resolver->resolveFromRequest($request));
    }
}
