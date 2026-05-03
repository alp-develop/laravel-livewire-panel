<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Unit;

use AlpDevelop\LivewirePanel\Search\SearchProviderInterface;
use AlpDevelop\LivewirePanel\Search\SearchRegistry;
use PHPUnit\Framework\TestCase;

final class SearchRegistryTest extends TestCase
{
    private SearchRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new SearchRegistry();
    }

    public function test_register_and_retrieve_for_panel(): void
    {
        $provider = $this->createMock(SearchProviderInterface::class);
        $this->registry->register('admin', $provider);

        $providers = $this->registry->forPanel('admin');

        $this->assertCount(1, $providers);
        $this->assertSame($provider, $providers[0]);
    }

    public function test_for_panel_returns_empty_for_unknown(): void
    {
        $this->assertSame([], $this->registry->forPanel('nonexistent'));
    }

    public function test_search_delegates_to_providers(): void
    {
        $provider = $this->createMock(SearchProviderInterface::class);
        $provider->method('category')->willReturn('Navigation');
        $provider->method('icon')->willReturn('fa-compass');
        $provider->method('search')->willReturn([
            ['label' => 'Dashboard', 'url' => '/admin'],
        ]);

        $this->registry->register('admin', $provider);

        $results = $this->registry->search('dash', 'admin');

        $this->assertCount(1, $results);
        $this->assertSame('Navigation', $results[0]['category']);
        $this->assertSame('fa-compass', $results[0]['icon']);
        $this->assertCount(1, $results[0]['items']);
    }

    public function test_search_skips_providers_with_no_results(): void
    {
        $emptyProvider = $this->createMock(SearchProviderInterface::class);
        $emptyProvider->method('search')->willReturn([]);

        $fullProvider = $this->createMock(SearchProviderInterface::class);
        $fullProvider->method('category')->willReturn('Pages');
        $fullProvider->method('icon')->willReturn('fa-file');
        $fullProvider->method('search')->willReturn([['label' => 'Home']]);

        $this->registry->register('admin', $emptyProvider);
        $this->registry->register('admin', $fullProvider);

        $results = $this->registry->search('home', 'admin');

        $this->assertCount(1, $results);
        $this->assertSame('Pages', $results[0]['category']);
    }

    public function test_search_returns_empty_for_unknown_panel(): void
    {
        $this->assertSame([], $this->registry->search('query', 'nonexistent'));
    }

    public function test_multiple_providers_per_panel(): void
    {
        $p1 = $this->createMock(SearchProviderInterface::class);
        $p2 = $this->createMock(SearchProviderInterface::class);

        $this->registry->register('admin', $p1);
        $this->registry->register('admin', $p2);

        $this->assertCount(2, $this->registry->forPanel('admin'));
    }

    public function test_search_memoizes_results(): void
    {
        $provider = $this->createMock(SearchProviderInterface::class);
        $provider->method('category')->willReturn('Navigation');
        $provider->method('icon')->willReturn('fa-compass');
        $provider->expects($this->once())
            ->method('search')
            ->willReturn([['label' => 'Dashboard', 'url' => '/admin']]);

        $this->registry->register('admin', $provider);

        $first  = $this->registry->search('dash', 'admin');
        $second = $this->registry->search('dash', 'admin');

        $this->assertSame($first, $second);
    }

    public function test_clear_cache_forces_re_evaluation(): void
    {
        $provider = $this->createMock(SearchProviderInterface::class);
        $provider->method('category')->willReturn('Navigation');
        $provider->method('icon')->willReturn('fa-compass');
        $provider->expects($this->exactly(2))
            ->method('search')
            ->willReturn([['label' => 'Dashboard', 'url' => '/admin']]);

        $this->registry->register('admin', $provider);

        $this->registry->search('dash', 'admin');
        $this->registry->clearCache();
        $this->registry->search('dash', 'admin');
    }
}
