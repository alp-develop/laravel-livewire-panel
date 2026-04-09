<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

use Livewire\Component;

abstract class AbstractWidget extends Component implements WidgetInterface
{
    public string $title = '';

    public int $pollSeconds = 0;

    public function canView(): bool
    {
        return true;
    }
}
