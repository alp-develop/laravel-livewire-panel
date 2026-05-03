<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;
use AlpDevelop\LivewirePanel\View\Components\DarkModeToggle;
use AlpDevelop\LivewirePanel\View\Components\LocaleSelector;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;

final class BladeComponentsTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('laravel-livewire-panel.panels.test.locale', [
            'enabled'   => true,
            'available' => ['en' => 'English', 'es' => 'Spanish'],
        ]);
    }

    public function test_dark_mode_toggle_disabled_by_default(): void
    {
        $toggle = new DarkModeToggle();
        $this->assertFalse($toggle->enabled);
        $this->assertFalse($toggle->shouldRender());
    }

    public function test_dark_mode_toggle_render_returns_view(): void
    {
        $toggle = new DarkModeToggle();
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $toggle->render());
    }

    public function test_locale_selector_variant_default_is_dropdown(): void
    {
        $selector = new LocaleSelector();
        $this->assertSame('dropdown', $selector->variant);
    }

    public function test_locale_selector_current_locale(): void
    {
        $selector = new LocaleSelector();
        $this->assertSame(app()->getLocale(), $selector->current);
    }

    public function test_locale_selector_enabled_when_configured(): void
    {
        $context = $this->app->make(PanelContext::class);
        $context->set('test');

        $selector = new LocaleSelector();
        $this->assertTrue($selector->enabled);
        $this->assertArrayHasKey('en', $selector->available);
        $this->assertArrayHasKey('es', $selector->available);
    }
}
