<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('panel::layouts.auth', ['title' => 'Create account'])]
final class RegisterComponent extends AbstractRegisterComponent
{
    public function render(): View
    {
        return view('panel::auth.register', [
            'hasLoginRoute' => Route::has("panel.{$this->panelId}.auth.login"),
        ]);
    }
}
