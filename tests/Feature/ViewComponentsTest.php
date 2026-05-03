<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\View\Components\Card;
use AlpDevelop\LivewirePanel\View\Components\DarkModeToggle;
use AlpDevelop\LivewirePanel\View\Components\LocaleSelector;
use AlpDevelop\LivewirePanel\View\Components\Portal;

final class ViewComponentsTest extends PanelTestCase
{
    public function test_card_component_instantiates_with_defaults(): void
    {
        $card = new Card();
        $this->assertSame('', $card->title);
    }

    public function test_card_component_sets_title(): void
    {
        $card = new Card(title: 'My Card');
        $this->assertSame('My Card', $card->title);
    }

    public function test_card_component_renders(): void
    {
        $card = new Card(title: 'Test');
        $view = $card->render();
        $this->assertNotNull($view);
    }

    public function test_dark_mode_toggle_disabled_by_default(): void
    {
        $toggle = new DarkModeToggle();
        $this->assertFalse($toggle->enabled);
        $this->assertFalse($toggle->shouldRender());
    }

    public function test_dark_mode_toggle_renders(): void
    {
        $toggle = new DarkModeToggle();
        $view   = $toggle->render();
        $this->assertNotNull($view);
    }

    public function test_portal_component_constructor(): void
    {
        $portal = new Portal(panel: 'admin', route: 'home', params: []);
        $this->assertSame('admin', $portal->panel);
        $this->assertSame('home', $portal->route);
    }

    public function test_portal_component_default_route(): void
    {
        $portal = new Portal(panel: 'admin');
        $this->assertSame('home', $portal->route);
    }

    public function test_portal_component_renders(): void
    {
        $portal = new Portal(panel: 'admin');
        $view   = $portal->render();
        $this->assertNotNull($view);
    }

    public function test_locale_selector_disabled_by_default(): void
    {
        $selector = new LocaleSelector();
        $this->assertFalse($selector->enabled);
        $this->assertFalse($selector->shouldRender());
    }

    public function test_locale_selector_default_variant(): void
    {
        $selector = new LocaleSelector();
        $this->assertSame('dropdown', $selector->variant);
    }

    public function test_locale_selector_renders(): void
    {
        $selector = new LocaleSelector();
        $view     = $selector->render();
        $this->assertNotNull($view);
    }
}
