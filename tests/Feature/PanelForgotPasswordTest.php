<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire\AbstractForgotPasswordComponent;
use AlpDevelop\LivewirePanel\Modules\Auth\Notifications\PanelForgotPasswordNotification;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

final class TestForgotPasswordUser extends Model
{
    use Notifiable;

    protected $fillable = ['id', 'name', 'email', 'password'];
    public $timestamps  = false;
}

final class TestForgotPasswordComponent extends AbstractForgotPasswordComponent
{
    public function render(): View
    {
        return view('panel::auth.forgot-password');
    }
}

final class PanelForgotPasswordTest extends PanelTestCase
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

    public function test_submit_with_nonexistent_email_sets_sent_true(): void
    {
        Password::shouldReceive('broker->getUser')
            ->once()
            ->andReturn(null);

        $component = Livewire::withoutLazyLoading()->test(TestForgotPasswordComponent::class)
            ->set('email', 'nonexistent@example.com')
            ->call('submit');

        $component->assertSet('sent', true);
    }

    public function test_submit_with_valid_email_sends_notification(): void
    {
        Notification::fake();

        $user = new TestForgotPasswordUser(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com']);

        Password::shouldReceive('broker->getUser')
            ->once()
            ->andReturn($user);

        Password::shouldReceive('broker->createToken')
            ->once()
            ->andReturn('test-token-123');

        Livewire::withoutLazyLoading()->test(TestForgotPasswordComponent::class)
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertSet('sent', true);

        Notification::assertSentTo($user, PanelForgotPasswordNotification::class);
    }

    public function test_submit_rate_limit_adds_error_after_five_attempts(): void
    {
        $throttleKey = 'panel-forgot-password:127.0.0.1';
        RateLimiter::clear($throttleKey);

        for ($i = 0; $i < 5; $i++) {
            RateLimiter::hit($throttleKey, 60);
        }

        Livewire::withoutLazyLoading()->test(TestForgotPasswordComponent::class)
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors(['email']);

        RateLimiter::clear($throttleKey);
    }

    public function test_submit_validates_email_field(): void
    {
        Livewire::withoutLazyLoading()->test(TestForgotPasswordComponent::class)
            ->set('email', 'not-an-email')
            ->call('submit')
            ->assertHasErrors(['email'])
            ->assertSet('sent', false);
    }

    public function test_submit_requires_email_field(): void
    {
        Livewire::withoutLazyLoading()->test(TestForgotPasswordComponent::class)
            ->set('email', '')
            ->call('submit')
            ->assertHasErrors(['email'])
            ->assertSet('sent', false);
    }
}
