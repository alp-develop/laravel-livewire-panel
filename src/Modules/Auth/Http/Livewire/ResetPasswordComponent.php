<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('panel::layouts.auth', ['title' => 'Reset password'])]
final class ResetPasswordComponent extends AbstractResetPasswordComponent
{
    public function render(): View
    {
        return view('panel::auth.reset-password', [
            'hasLoginRoute' => Route::has("panel.{$this->panelId}.auth.login"),
        ]);
    }
}
