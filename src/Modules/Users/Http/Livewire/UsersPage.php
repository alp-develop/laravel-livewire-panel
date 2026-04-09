<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Users\Http\Livewire;

use AlpDevelop\LivewirePanel\Auth\PanelGate;
use AlpDevelop\LivewirePanel\Events\UserCreated;
use AlpDevelop\LivewirePanel\Events\UserDeleted;
use AlpDevelop\LivewirePanel\Events\UserUpdated;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\Security\SearchQuerySanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('panel::layouts.app', ['title' => 'Users'])]
final class UsersPage extends Component
{
    use WithPagination;

    public string $search    = '';
    public bool   $showForm  = false;

    #[Locked]
    public ?int   $editingId = null;

    #[Locked]
    public ?int   $deletingId = null;

    public string $formName     = '';
    public string $formEmail    = '';
    public string $formPassword = '';

    public function mount(): void
    {
        $this->authorizePanelAction('users.view');
    }

    public function updatedSearch(): void
    {
        $this->search = mb_substr($this->search, 0, 100);
        $this->resetPage();
    }

    public function newUser(): void
    {
        $this->authorizePanelAction('users.create');
        $this->resetForm();
        $this->showForm  = true;
        $this->editingId = null;
    }

    public function editUser(int $id): void
    {
        $this->authorizePanelAction('users.edit');
        $user            = $this->userModel()::findOrFail($id);
        $this->formName  = $user->name;
        $this->formEmail = $user->email;
        $this->formPassword = '';
        $this->editingId = $id;
        $this->showForm  = true;
        $this->resetErrorBag();
    }

    public function saveUser(): void
    {
        if ($this->editingId) {
            $this->authorizePanelAction('users.edit');

            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = new ($this->userModel());
            $table = $model->getTable();

            $this->validate([
                'formName'     => 'required|string|max:255',
                'formEmail'    => "required|email|max:255|unique:{$table},email,{$this->editingId}",
                'formPassword' => ['nullable', 'string', Password::min(8)->mixedCase()->numbers()],
            ]);

            $data = ['name' => $this->formName, 'email' => $this->formEmail];
            if ($this->formPassword !== '') {
                $data['password'] = Hash::make($this->formPassword);
            }

            $this->userModel()::findOrFail($this->editingId)->update($data);

            $panelId = app(PanelContext::class)->get();
            event(new UserUpdated($panelId, (int) $this->editingId, (int) auth()->id(), request()->ip()));
        } else {
            $this->authorizePanelAction('users.create');

            /** @var \Illuminate\Database\Eloquent\Model $model */
            $model = new ($this->userModel());
            $table = $model->getTable();

            $this->validate([
                'formName'     => 'required|string|max:255',
                'formEmail'    => "required|email|max:255|unique:{$table},email",
                'formPassword' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
            ]);

            $newUser = $this->userModel()::create([
                'name'     => $this->formName,
                'email'    => $this->formEmail,
                'password' => Hash::make($this->formPassword),
            ]);

            $panelId = app(PanelContext::class)->get();
            event(new UserCreated($panelId, (int) $newUser->getKey(), $this->formEmail, (int) auth()->id(), request()->ip()));
        }

        $this->resetForm();
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->authorizePanelAction('users.delete');
        $this->deletingId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    public function deleteUser(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        $this->authorizePanelAction('users.delete');
        $this->userModel()::findOrFail($this->deletingId)->delete();

        $panelId = app(PanelContext::class)->get();
        event(new UserDeleted($panelId, (int) $this->deletingId, (int) auth()->id(), request()->ip()));

        $this->deletingId = null;
    }

    public function render(): View
    {
        $searchTerm = SearchQuerySanitizer::sanitize($this->search);

        $users = $this->userModel()::query()
            ->when($searchTerm !== '', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('panel::modules.users.page', ['users' => $users]);
    }

    private function userModel(): string
    {
        return config('auth.providers.users.model', \App\Models\User::class);
    }

    private function resetForm(): void
    {
        $this->showForm     = false;
        $this->editingId    = null;
        $this->formName     = '';
        $this->formEmail    = '';
        $this->formPassword = '';
        $this->resetErrorBag();
    }

    private function authorizePanelAction(string $permission): void
    {
        $gate = app(PanelGate::class);

        if ($gate->denies($permission)) {
            abort(403);
        }
    }
}
