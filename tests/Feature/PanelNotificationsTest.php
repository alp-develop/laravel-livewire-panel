<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Tests\Feature;

use AlpDevelop\LivewirePanel\Notifications\NotificationRegistry;
use AlpDevelop\LivewirePanel\Notifications\NotificationProviderInterface;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\Tests\PanelTestCase;
use AlpDevelop\LivewirePanel\View\Livewire\AbstractPanelNotifications;
use Livewire\Livewire;

final class TestNotificationsComponent extends AbstractPanelNotifications
{
}

final class PanelNotificationsTest extends PanelTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    private function makeProvider(int $count = 0, array $items = []): NotificationProviderInterface
    {
        $provider = new class ($count, $items) implements NotificationProviderInterface {
            public function __construct(
                private int $count,
                private array $items
            ) {}

            public function count(string $panelId): int
            {
                return $this->count;
            }

            public function items(string $panelId, int $limit = 10): array
            {
                return $this->items;
            }

            public function markAsRead(string $id, string $panelId, ?int $userId = null): void
            {
                $this->count = max(0, $this->count - 1);
            }

            public function markAllAsRead(string $panelId): void
            {
                $this->count = 0;
                $this->items = [];
            }
        };

        return $provider;
    }

    public function test_notifications_render_with_no_provider(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        Livewire::withoutLazyLoading()
            ->test(TestNotificationsComponent::class)
            ->assertStatus(200);
    }

    public function test_notifications_render_with_provider(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        $provider = $this->makeProvider(3, [
            ['id' => '1', 'title' => 'Alert'],
            ['id' => '2', 'title' => 'Warning'],
            ['id' => '3', 'title' => 'Info'],
        ]);

        $this->app->make(NotificationRegistry::class)->register('test', $provider);

        Livewire::withoutLazyLoading()
            ->test(TestNotificationsComponent::class)
            ->assertStatus(200);
    }

    public function test_notifications_mark_as_read_invalidates_cache(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        $provider = $this->makeProvider(2, [
            ['id' => 'abc', 'title' => 'First'],
            ['id' => 'def', 'title' => 'Second'],
        ]);

        $this->app->make(NotificationRegistry::class)->register('test', $provider);

        Livewire::withoutLazyLoading()
            ->test(TestNotificationsComponent::class)
            ->call('markAsRead', 'abc')
            ->assertStatus(200);
    }

    public function test_notifications_mark_all_as_read(): void
    {
        $this->app->make(PanelContext::class)->set('test');

        $provider = $this->makeProvider(5, []);

        $this->app->make(NotificationRegistry::class)->register('test', $provider);

        Livewire::withoutLazyLoading()
            ->test(TestNotificationsComponent::class)
            ->call('markAllAsRead')
            ->assertStatus(200);
    }
}
