<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Search\NavigationSearchProvider;
use AlpDevelop\LivewirePanel\Modules\NavigationItem;
use AlpDevelop\LivewirePanel\Navigation\NavigationGroup;
use AlpDevelop\LivewirePanel\Navigation\NavigationRegistry;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class NavigationSearchProviderTest extends PanelTestCase
{
    public function test_category_is_pages(): void
    {
        $provider = new NavigationSearchProvider();
        $this->assertSame('Pages', $provider->category());
    }

    public function test_icon_returns_layer_group(): void
    {
        $provider = new NavigationSearchProvider();
        $this->assertSame('layer-group', $provider->icon());
    }

    public function test_search_returns_empty_when_no_nav_items(): void
    {
        $provider = new NavigationSearchProvider();
        $results  = $provider->search('dashboard', 'test');

        $this->assertIsArray($results);
    }

    public function test_search_returns_empty_array_for_unknown_panel(): void
    {
        $provider = new NavigationSearchProvider();
        $results  = $provider->search('', 'unknown-panel');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function test_search_filters_by_label(): void
    {
        $registry = $this->app->make(NavigationRegistry::class);
        $registry->add('test', new NavigationItem(
            label: 'Dashboard',
            route: 'panel.test.home',
            icon: 'house',
        ));

        $provider = new NavigationSearchProvider();
        $results  = $provider->search('dash', 'test');

        $this->assertIsArray($results);
    }

    public function test_search_returns_all_when_query_empty(): void
    {
        $provider = new NavigationSearchProvider();
        $results  = $provider->search('', 'test');

        $this->assertIsArray($results);
    }

    public function test_search_with_registered_route_returns_result(): void
    {
        $registry = $this->app->make(NavigationRegistry::class);
        $registry->add('test', new NavigationItem(
            label: 'Login Page',
            route: 'panel.test.auth.login',
            icon:  'house',
            description: 'Login',
        ));

        $provider = new NavigationSearchProvider();
        $results  = $provider->search('login', 'test');

        $this->assertNotEmpty($results);
        $this->assertSame('Login Page', $results[0]['label']);
        $this->assertArrayHasKey('url', $results[0]);
        $this->assertArrayHasKey('icon', $results[0]);
    }

    public function test_search_group_children_with_registered_routes(): void
    {
        $registry = $this->app->make(NavigationRegistry::class);
        $child    = new NavigationItem(label: 'Forgot Password', route: 'panel.test.auth.forgot-password', icon: 'cog');
        $group    = new NavigationGroup(label: 'Auth', children: [$child], icon: 'lock');
        $registry->addGroup('test', $group);

        $provider = new NavigationSearchProvider();
        $results  = $provider->search('forgot', 'test');

        $this->assertNotEmpty($results);
    }

    public function test_search_limits_to_15_results(): void
    {
        $registry = $this->app->make(NavigationRegistry::class);

        for ($i = 1; $i <= 20; $i++) {
            $registry->add('test', new NavigationItem(
                label: "Login Page {$i}",
                route: 'panel.test.auth.login',
                icon:  'file',
            ));
        }

        $provider = new NavigationSearchProvider();
        $results  = $provider->search('login', 'test');

        $this->assertLessThanOrEqual(15, count($results));
    }

    public function test_search_with_no_route_match_returns_empty(): void
    {
        $registry = $this->app->make(NavigationRegistry::class);
        $registry->add('test', new NavigationItem(
            label: 'Ghost Page',
            route: 'panel.test.nonexistent',
            icon:  'ghost',
        ));

        $provider = new NavigationSearchProvider();
        $results  = $provider->search('ghost', 'test');

        $this->assertEmpty($results);
    }
}
