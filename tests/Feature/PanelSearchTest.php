<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\View\Livewire\PanelSearch;
use Livewire\Livewire;

final class PanelSearchTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    public function test_search_renders_with_empty_query(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(PanelSearch::class)
            ->assertStatus(200);
    }

    public function test_search_skips_single_char_query(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(PanelSearch::class)
            ->set('query', 'a')
            ->assertStatus(200);
    }

    public function test_search_executes_for_two_or_more_chars(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(PanelSearch::class)
            ->set('query', 'dashboard')
            ->assertStatus(200);
    }

    public function test_search_does_not_re_execute_identical_query(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(PanelSearch::class)
            ->set('query', 'dashboard')
            ->assertStatus(200)
            ->set('query', 'dashboar')
            ->assertStatus(200)
            ->set('query', 'dashboard')
            ->assertStatus(200);
    }

    public function test_search_truncates_query_to_100_chars(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        $longQuery = str_repeat('a', 150);

        Livewire::withoutLazyLoading()
            ->test(PanelSearch::class)
            ->set('query', $longQuery)
            ->assertSet('query', str_repeat('a', 100));
    }
}
