<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Errors\Http\Livewire\NotFoundComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Livewire\Livewire;

final class NotFoundComponentTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    public function test_not_found_component_renders(): void
    {
        Livewire::withoutLazyLoading()
            ->test(NotFoundComponent::class)
            ->assertStatus(200);
    }

    public function test_not_found_component_has_panel_id(): void
    {
        $component = Livewire::withoutLazyLoading()
            ->test(NotFoundComponent::class);

        $this->assertIsString($component->get('panelId'));
    }
}
