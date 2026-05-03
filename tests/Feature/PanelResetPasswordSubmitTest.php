<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractResetPasswordComponent;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

final class TestResetSubmitComponent extends AbstractResetPasswordComponent
{
    public function render(): View
    {
        return view('panel::auth.reset-password');
    }
}

final class PanelResetPasswordSubmitTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('auth.passwords.users', [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__ . '/../../vendor/orchestra/testbench-core/laravel/migrations'
        );
    }

    public function test_submit_success_resets_password(): void
    {
        Event::fake();

        $user = User::create([
            'name'     => 'Test',
            'email'    => 'reset@example.com',
            'password' => Hash::make('OldPass1'),
        ]);

        Password::shouldReceive('broker')
            ->andReturnSelf();

        Password::shouldReceive('getUser')
            ->andReturn($user);

        Password::shouldReceive('tokenExists')
            ->andReturn(true);

        Password::shouldReceive('reset')
            ->once()
            ->andReturnUsing(function (array $credentials, \Closure $callback) use ($user) {
                $callback($user, $credentials['password']);
                return Password::PASSWORD_RESET;
            });

        Livewire::withoutLazyLoading()
            ->withQueryParams(['email' => 'reset@example.com'])
            ->test(TestResetSubmitComponent::class, ['token' => 'valid-token'])
            ->set('email', 'reset@example.com')
            ->set('password', 'NewPass123')
            ->set('password_confirmation', 'NewPass123')
            ->call('submit')
            ->assertRedirect();
    }

    public function test_submit_invalid_token_shows_error(): void
    {
        $user = User::create([
            'name'     => 'Test',
            'email'    => 'reset2@example.com',
            'password' => Hash::make('OldPass1'),
        ]);

        Password::shouldReceive('broker')
            ->andReturnSelf();

        Password::shouldReceive('getUser')
            ->andReturn($user);

        Password::shouldReceive('tokenExists')
            ->andReturn(true);

        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        Livewire::withoutLazyLoading()
            ->withQueryParams(['email' => 'reset2@example.com'])
            ->test(TestResetSubmitComponent::class, ['token' => 'valid-token'])
            ->set('email', 'reset2@example.com')
            ->set('password', 'NewPass123')
            ->set('password_confirmation', 'NewPass123')
            ->call('submit')
            ->assertHasErrors(['email']);
    }
}
