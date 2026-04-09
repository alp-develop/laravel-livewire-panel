<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Helpers;

use AlpDevelop\LivewirePanel\Modules\ModuleRegistry;
use AlpDevelop\LivewirePanel\Themes\ThemeRegistry;
use AlpDevelop\LivewirePanel\Widgets\WidgetRegistry;
use Illuminate\Foundation\Auth\User;

trait PanelTestHelpers
{
    protected function actingAsPanelUser(array $attributes = []): static
    {
        $user = $this->makePanelUser($attributes);
        $this->actingAs($user, 'web');

        return $this;
    }

    protected function assertWidgetRegistered(string $alias): void
    {
        $registry = $this->app->make(WidgetRegistry::class);
        $this->assertTrue(
            $registry->has($alias),
            "Widget [{$alias}] no está registrado en WidgetRegistry."
        );
    }

    protected function assertModuleRegistered(string $panelId, string $moduleClass): void
    {
        $registry = $this->app->make(ModuleRegistry::class);
        $modules  = $registry->forPanel($panelId);

        $this->assertContains(
            $moduleClass,
            $modules,
            "Módulo [{$moduleClass}] no está registrado en el panel [{$panelId}]."
        );
    }

    protected function assertPanelRouteExists(string $name): void
    {
        $this->assertTrue(
            $this->app['router']->has($name),
            "La ruta de panel [{$name}] no existe."
        );
    }

    protected function assertThemeRegistered(string $themeId): void
    {
        $registry = $this->app->make(ThemeRegistry::class);
        $this->assertTrue(
            $registry->has($themeId),
            "Tema [{$themeId}] no está registrado en ThemeRegistry."
        );
    }

    protected function assertCssSanitized(string $output): void
    {
        $this->assertStringNotContainsString(';background', $output, 'CSS injection via semicolon detected');
        $this->assertStringNotContainsString('<script>', $output, 'Script injection detected');
        $this->assertStringNotContainsString('{color', $output, 'CSS injection via curly brace detected');
    }
}
