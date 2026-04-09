<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use AlpDevelop\LivewirePanel\Widgets\StatsCardWidget;

final class WidgetAndModuleTest extends PanelTestCase
{
    public function test_widget_registry_can_be_resolved(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);

        $this->assertInstanceOf(WidgetRegistry::class, $registry);
    }

    public function test_widget_registry_is_singleton(): void
    {
        $a = $this->app->make(WidgetRegistry::class);
        $b = $this->app->make(WidgetRegistry::class);

        $this->assertSame($a, $b);
    }

    public function test_builtin_widget_stats_card_is_registered(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);

        $this->assertTrue($registry->has('stats-card'));
    }

    public function test_builtin_widget_chart_widget_is_registered(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);

        $this->assertTrue($registry->has('chart-widget'));
    }

    public function test_builtin_widget_recent_table_is_registered(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);

        $this->assertTrue($registry->has('recent-table'));
    }

    public function test_widget_registry_returns_all_widgets(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $all      = $registry->all();

        $this->assertIsArray($all);
        $this->assertArrayHasKey('stats-card', $all);
        $this->assertArrayHasKey('chart-widget', $all);
        $this->assertArrayHasKey('recent-table', $all);
    }

    public function test_widget_can_be_registered_manually(): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $registry->register('custom-widget', StatsCardWidget::class);

        $this->assertTrue($registry->has('custom-widget'));
    }

    public function test_module_registry_can_be_resolved(): void
    {
        $registry = $this->app->make(ModuleRegistry::class);

        $this->assertInstanceOf(ModuleRegistry::class, $registry);
    }

    public function test_module_registry_is_singleton(): void
    {
        $a = $this->app->make(ModuleRegistry::class);
        $b = $this->app->make(ModuleRegistry::class);

        $this->assertSame($a, $b);
    }

    public function test_module_can_be_registered_for_panel(): void
    {
        $registry = $this->app->make(ModuleRegistry::class);
        $registry->register('test', StatsCardWidget::class);

        $modules = $registry->forPanel('test');

        $this->assertContains(StatsCardWidget::class, $modules);
    }

    public function test_module_registry_returns_empty_for_unknown_panel(): void
    {
        $registry = $this->app->make(ModuleRegistry::class);
        $modules  = $registry->forPanel('unknown-panel');

        $this->assertIsArray($modules);
        $this->assertEmpty($modules);
    }

    public function test_module_registry_returns_all(): void
    {
        $registry = $this->app->make(ModuleRegistry::class);
        $all      = $registry->all();

        $this->assertIsArray($all);
    }
}
