<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Http\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;

#[Layout('panel::layouts.auth', ['title' => 'Forgot password'])]
final class ForgotPasswordComponent extends AbstractForgotPasswordComponent
{
    public function render(): View
    {
        return view('panel::auth.forgot-password');
    }
}
