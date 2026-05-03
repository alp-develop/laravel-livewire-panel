<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\View\Livewire;

use AlpDevelop\LivewirePanel\Notifications\NotificationRegistry;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class AbstractPanelNotifications extends Component
{
    #[Locked]
    public string $panelId = '';

    #[Locked]
    public bool $polling = true;

    #[Locked]
    public int $pollingInterval = 30;

    private ?int $cachedCount = null;

    /** @var array<int, mixed>|null */
    private ?array $cachedItems = null;

    public function mount(bool $polling = true, int $pollingInterval = 30): void
    {
        $this->polling         = $polling;
        $this->pollingInterval = $pollingInterval;

        $context       = app(PanelContext::class);
        $this->panelId = $context->resolved()
            ? $context->get()
            : app(PanelResolver::class)->resolveFromRequest(request());
    }

    public function markAsRead(string $id): void
    {
        $provider = app(NotificationRegistry::class)->resolve($this->panelId);

        if ($provider) {
            $userId = auth()->id();
            $provider->markAsRead($id, $this->panelId, is_int($userId) ? $userId : null);
        }

        $this->cachedCount = null;
        $this->cachedItems = null;
    }

    public function markAllAsRead(): void
    {
        $provider = app(NotificationRegistry::class)->resolve($this->panelId);

        if ($provider) {
            $provider->markAllAsRead($this->panelId);
        }

        $this->cachedCount = null;
        $this->cachedItems = null;
    }

    public function render(): View
    {
        if ($this->cachedCount === null) {
            $provider          = app(NotificationRegistry::class)->resolve($this->panelId);
            $this->cachedCount = $provider ? $provider->count($this->panelId) : 0;
            $this->cachedItems = $provider ? $provider->items($this->panelId) : [];
        }

        return view($this->view(), [
            'count'           => $this->cachedCount,
            'items'           => $this->cachedItems ?? [],
            'polling'         => $this->polling,
            'pollingInterval' => $this->pollingInterval,
        ]);
    }

    protected function view(): string
    {
        return 'panel::livewire.notifications';
    }
}
