<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractResetPasswordComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

final class TestResetPasswordComponent extends AbstractResetPasswordComponent
{
    public function render(): View
    {
        return view('panel::auth.reset-password');
    }
}

final class PanelResetPasswordTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('auth.passwords.users', [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ]);
    }

    public function test_mount_with_invalid_token_redirects_to_login(): void
    {
        Password::shouldReceive('broker->getUser')
            ->once()
            ->andReturn(null);

        Livewire::withoutLazyLoading()
            ->test(TestResetPasswordComponent::class, ['token' => 'invalid-token'])
            ->assertRedirect();
    }

    public function test_mount_populates_token_and_email(): void
    {
        $user = $this->makePanelUser();

        Password::shouldReceive('broker->getUser')
            ->once()
            ->andReturn($user);

        Password::shouldReceive('broker->tokenExists')
            ->once()
            ->andReturn(true);

        $component = Livewire::withoutLazyLoading()
            ->withQueryParams(['email' => 'test@example.com'])
            ->test(TestResetPasswordComponent::class, ['token' => 'valid-token']);

        $component->assertSet('token', 'valid-token');
        $component->assertSet('email', 'test@example.com');
    }

    public function test_submit_validates_password_required(): void
    {
        $user = $this->makePanelUser();

        Password::shouldReceive('broker->getUser')
            ->andReturn($user);

        Password::shouldReceive('broker->tokenExists')
            ->andReturn(true);

        Livewire::withoutLazyLoading()
            ->withQueryParams(['email' => 'test@example.com'])
            ->test(TestResetPasswordComponent::class, ['token' => 'valid-token'])
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('submit')
            ->assertHasErrors(['password']);
    }

    public function test_submit_validates_email_required(): void
    {
        $user = $this->makePanelUser();

        Password::shouldReceive('broker->getUser')
            ->andReturn($user);

        Password::shouldReceive('broker->tokenExists')
            ->andReturn(true);

        Livewire::withoutLazyLoading()
            ->withQueryParams(['email' => 'test@example.com'])
            ->test(TestResetPasswordComponent::class, ['token' => 'valid-token'])
            ->set('email', '')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    public function test_submit_rate_limit_adds_error_after_five_attempts(): void
    {
        $user = $this->makePanelUser();

        Password::shouldReceive('broker->getUser')
            ->andReturn($user);

        Password::shouldReceive('broker->tokenExists')
            ->andReturn(true);

        $throttleKey = 'panel-reset-password:127.0.0.1';
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        Livewire::withoutLazyLoading()
            ->withQueryParams(['email' => 'test@example.com'])
            ->test(TestResetPasswordComponent::class, ['token' => 'valid-token'])
            ->set('email', 'test@example.com')
            ->set('password', 'newpassword123')
            ->set('password_confirmation', 'newpassword123')
            ->call('submit')
            ->assertHasErrors(['email']);

        RateLimiter::clear($throttleKey);
    }
}
