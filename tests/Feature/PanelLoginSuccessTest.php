<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractLoginComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Contracts\View\View;
use Livewire\Livewire;

final class TestLoginFlowComponent extends AbstractLoginComponent
{
    public function render(): View
    {
        return view('panel::auth.login');
    }
}

final class PanelLoginSuccessTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/migrations'
        );
    }

    public function test_invalid_credentials_show_error(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestLoginFlowComponent::class)
            ->set('email', 'wrong@example.com')
            ->set('password', 'WrongPassword1')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_empty_password_fails_validation(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestLoginFlowComponent::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password']);
    }
}
