<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractRegisterComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

final class TestRegisterComponent extends AbstractRegisterComponent
{
    public function render(): View
    {
        return view('panel::auth.register');
    }
}

final class PanelRegisterTest extends PanelTestCase
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

    public function test_register_validates_name_required(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestRegisterComponent::class)
            ->set('name', '')
            ->set('email', 'test@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['name']);
    }

    public function test_register_validates_email_required(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestRegisterComponent::class)
            ->set('name', 'Test User')
            ->set('email', '')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_register_validates_email_format(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestRegisterComponent::class)
            ->set('name', 'Test User')
            ->set('email', 'not-an-email')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_register_validates_password_required(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestRegisterComponent::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    public function test_register_rate_limit_adds_error_after_five_attempts(): void
    {
        $throttleKey = 'panel-register:127.0.0.1';
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        Livewire::withoutLazyLoading()
            ->test(TestRegisterComponent::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'Password1')
            ->set('password_confirmation', 'Password1')
            ->call('register')
            ->assertHasErrors(['email']);

        RateLimiter::clear($throttleKey);
    }
}
