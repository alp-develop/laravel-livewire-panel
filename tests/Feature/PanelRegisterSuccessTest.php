<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractRegisterComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Contracts\View\View;
use Livewire\Livewire;

final class TestSuccessRegisterComponent extends AbstractRegisterComponent
{
    public function render(): View
    {
        return view('panel::auth.register');
    }
}

final class PanelRegisterSuccessTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('laravel-livewire-panel.panels.test.registration', true);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/migrations'
        );
    }

    public function test_register_validates_password_confirmation(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestSuccessRegisterComponent::class)
            ->set('name', 'User')
            ->set('email', 'user@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Different1')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_register_validates_required_email(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestSuccessRegisterComponent::class)
            ->set('name', 'User')
            ->set('email', '')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_register_validates_required_name(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestSuccessRegisterComponent::class)
            ->set('name', '')
            ->set('email', 'user@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['name']);
    }
}
