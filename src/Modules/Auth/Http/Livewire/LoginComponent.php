<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('panel::layouts.auth', ['title' => 'Sign in'])]
final class LoginComponent extends AbstractLoginComponent
{
    public function render(): View
    {
        return view('panel::auth.login', [
            'hasForgotRoute'   => Route::has("panel.{$this->panelId}.auth.forgot-password"),
            'hasRegisterRoute' => Route::has("panel.{$this->panelId}.auth.register"),
        ]);
    }
}
