<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Widgets;

use Livewire\Component;

/**
 * Base Livewire component for panel widgets.
 *
 * Extend this class and implement `render()` to create a widget.
 * Override `canView()` to restrict visibility by permission or condition.
 * Set `$pollSeconds` to enable automatic polling refresh.
 *
 * @see WidgetInterface
 */
abstract class AbstractWidget extends Component implements WidgetInterface
{
    public string $title = '';

    public int $pollSeconds = 0;

    public function canView(): bool
    {
        return true;
    }
}
