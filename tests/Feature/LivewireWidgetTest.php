<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Widgets\ChartWidget;
use AlpDevelop\LivewirePanel\Widgets\RecentTableWidget;
use AlpDevelop\LivewirePanel\Widgets\StatsCardWidget;
use Livewire\Livewire;

final class LivewireWidgetTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    public function test_stats_card_renders_with_properties(): void
    {
        Livewire::withoutLazyLoading()->test(StatsCardWidget::class, [
            'title'     => 'Total Users',
            'value'     => '1,482',
            'icon'      => 'users',
            'trend'     => '+12%',
            'trendType' => 'up',
            'color'     => 'primary',
        ])
            ->assertSee('Total Users')
            ->assertSee('1,482')
            ->assertSee('+12%')
            ->assertStatus(200);
    }

    public function test_stats_card_renders_without_trend(): void
    {
        Livewire::withoutLazyLoading()->test(StatsCardWidget::class, [
            'title' => 'Revenue',
            'value' => '$5,000',
        ])
            ->assertSee('Revenue')
            ->assertSee('$5,000')
            ->assertStatus(200);
    }

    public function test_chart_widget_renders_with_data(): void
    {
        Livewire::withoutLazyLoading()->test(ChartWidget::class, [
            'title'    => 'Sales',
            'type'     => 'bar',
            'labels'   => ['Jan', 'Feb', 'Mar'],
            'datasets' => [['data' => [100, 200, 150]]],
            'height'   => 300,
        ])
            ->assertSee('Sales')
            ->assertStatus(200);
    }

    public function test_recent_table_renders_with_rows(): void
    {
        Livewire::withoutLazyLoading()->test(RecentTableWidget::class, [
            'title'   => 'Recent Orders',
            'headers' => ['ID', 'Product', 'Status'],
            'rows'    => [
                ['1', 'Widget A', 'Pending'],
                ['2', 'Widget B', 'Completed'],
            ],
            'limit' => 5,
        ])
            ->assertSee('Recent Orders')
            ->assertSee('Widget A')
            ->assertSee('Completed')
            ->assertStatus(200);
    }

    public function test_recent_table_shows_empty_text(): void
    {
        Livewire::withoutLazyLoading()->test(RecentTableWidget::class, [
            'title'     => 'Empty Table',
            'headers'   => ['ID'],
            'rows'      => [],
            'emptyText' => 'No records found',
        ])
            ->assertSee('No records found')
            ->assertStatus(200);
    }

    public function test_recent_table_respects_limit(): void
    {
        Livewire::withoutLazyLoading()->test(RecentTableWidget::class, [
            'title'   => 'Limited',
            'headers' => ['Name'],
            'rows'    => [
                ['Row 1'],
                ['Row 2'],
                ['Row 3'],
            ],
            'limit' => 2,
        ])
            ->assertSee('Row 1')
            ->assertSee('Row 2')
            ->assertDontSee('Row 3')
            ->assertStatus(200);
    }

    public function test_widget_default_can_view_returns_true(): void
    {
        Livewire::withoutLazyLoading();

        $component = Livewire::test(StatsCardWidget::class, [
            'title' => 'Test',
            'value' => '0',
        ]);

        $this->assertTrue($component->instance()->canView());
    }
}
