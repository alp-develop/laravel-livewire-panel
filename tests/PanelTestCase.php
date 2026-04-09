<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests;

use AlpDevelop\LivewirePanel\LivewirePanelServiceProvider;
use AlpDevelop\LivewirePanel\Tests\Helpers\PanelTestHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class PanelTestCase extends TestCase
{
    use PanelTestHelpers;

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewirePanelServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('laravel-livewire-panel.default', 'test');
        $app['config']->set('laravel-livewire-panel.panels.test', [
            'id'            => 'test',
            'prefix'        => 'test-panel',
            'guard'         => 'web',
            'theme'         => 'bootstrap5',
            'customization' => 'style_table',
            'middleware'    => ['web'],
            'gate'          => null,
        ]);

        $app['config']->set('auth.guards.web', [
            'driver'   => 'session',
            'provider' => 'users',
        ]);

        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model'  => User::class,
        ]);

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard();
    }

    protected function makePanelUser(array $attributes = []): User
    {
        $user = new User();
        $user->forceFill(array_merge([
            'id'       => 1,
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
        ], $attributes));

        return $user;
    }
}
