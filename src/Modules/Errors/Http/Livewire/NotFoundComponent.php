<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Errors\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

#[Layout('panel::layouts.app', ['title' => '404 Not Found'])]
final class NotFoundComponent extends AbstractNotFoundComponent
{
    public function render(): View
    {
        return view('panel::errors.not-found');
    }
}
