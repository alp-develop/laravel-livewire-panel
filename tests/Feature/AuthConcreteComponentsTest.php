<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ForgotPasswordComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\LoginComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\RegisterComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\ResetPasswordComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Livewire\Livewire;

final class AuthConcreteComponentsTest extends PanelTestCase
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

    public function test_login_component_renders(): void
    {
        Livewire::withoutLazyLoading()
            ->test(LoginComponent::class)
            ->assertStatus(200);
    }

    public function test_register_component_renders(): void
    {
        $this->app['config']->set('laravel-livewire-panel.panels.test.registration', true);

        Livewire::withoutLazyLoading()
            ->test(RegisterComponent::class)
            ->assertStatus(200);
    }

    public function test_forgot_password_component_renders(): void
    {
        Livewire::withoutLazyLoading()
            ->test(ForgotPasswordComponent::class)
            ->assertStatus(200);
    }

    public function test_reset_password_component_redirects_on_invalid_token(): void
    {
        Livewire::withoutLazyLoading()
            ->test(ResetPasswordComponent::class, ['token' => 'invalid-token'])
            ->assertRedirect();
    }
}
