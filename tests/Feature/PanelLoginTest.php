<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractLoginComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Event;
use AlpDevelop\LivewirePanel\Events\LoginAttempted;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

final class TestLoginComponent extends AbstractLoginComponent
{
    public function render(): View
    {
        return view('panel::auth.login');
    }
}

final class PanelLoginTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/migrations'
        );
    }

    public function test_login_validates_email_required(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestLoginComponent::class)
            ->set('email', '')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_login_validates_email_format(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestLoginComponent::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_login_validates_password_required(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestLoginComponent::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    public function test_login_with_invalid_credentials_adds_error(): void
    {
        Livewire::withoutLazyLoading()
            ->test(TestLoginComponent::class)
            ->set('email', 'wrong@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_login_rate_limit_adds_error_after_five_attempts(): void
    {
        $throttleKey = 'panel-login:' . sha1('test@example.com') . ':127.0.0.1';
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        Livewire::withoutLazyLoading()
            ->test(TestLoginComponent::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email']);

        RateLimiter::clear($throttleKey);
    }

    public function test_login_with_invalid_credentials_fires_login_attempted_event(): void
    {
        Event::fake();

        Livewire::withoutLazyLoading()
            ->test(TestLoginComponent::class)
            ->set('email', 'wrong@example.com')
            ->set('password', 'wrongpassword')
            ->call('login');

        Event::assertDispatched(LoginAttempted::class, function (LoginAttempted $event) {
            return $event->successful === false;
        });
    }
}
