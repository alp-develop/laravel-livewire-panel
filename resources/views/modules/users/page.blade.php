<div>
<div class="panel-page-header" style="display:flex;align-items:center;justify-content:space-between">
    <h1>{{ __('panel::messages.users') }}</h1>
    <button wire:click="newUser" style="display:inline-flex;align-items:center;gap:.5rem;background:var(--panel-primary,#4f46e5);color:#fff;border:none;border-radius:var(--panel-radius,8px);padding:.55rem 1.1rem;font-size:.875rem;font-weight:600;cursor:pointer">
        <x-panel::icon name="plus" size="16" />
        {{ __('panel::messages.new_user') }}
    </button>
</div>

<div class="panel-section-card">
    <div class="panel-section-header" style="justify-content:space-between">
        <span>{{ __('panel::messages.user_list') }}</span>
        <div style="position:relative">
            <x-panel::icon name="magnifying-glass" size="16" style="position:absolute;left:.6rem;top:50%;transform:translateY(-50%);color:var(--panel-text-muted,#94a3b8);pointer-events:none" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('panel::messages.search_placeholder') }}"
                style="padding:.45rem .75rem .45rem 2rem;border:1px solid var(--panel-card-border,#e2e8f0);border-radius:var(--panel-radius,8px);font-size:.875rem;color:var(--panel-text-primary,#334155);background:var(--panel-card-bg,#fff);outline:none;min-width:220px"
            >
        </div>
    </div>
    <div style="overflow-x:auto">
        <table class="widget-table">
            <thead>
                <tr>
                    <th>{{ __('panel::messages.name') }}</th>
                    <th>{{ __('panel::messages.email') }}</th>
                    <th>{{ __('panel::messages.created') }}</th>
                    <th style="text-align:right">{{ __('panel::messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td style="font-weight:500;color:var(--panel-text-primary,#1e293b)">{{ $user->name }}</td>
                        <td style="color:var(--panel-text-muted,#64748b)">{{ $user->email }}</td>
                        <td style="color:var(--panel-text-muted,#94a3b8);font-size:.8rem">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td style="text-align:right">
                            @if ($deletingId === $user->id)
                                <span style="font-size:.82rem;color:var(--panel-text-muted,#64748b);margin-right:.5rem">{{ __('panel::messages.confirm_question') }}</span>
                                <button wire:click="deleteUser" style="background:#dc2626;color:#fff;border:none;border-radius:6px;padding:.3rem .7rem;font-size:.78rem;font-weight:600;cursor:pointer;margin-right:.25rem">{{ __('panel::messages.delete') }}</button>
                                <button wire:click="cancelDelete" style="background:var(--panel-content-bg,#f1f5f9);color:var(--panel-text-muted,#64748b);border:none;border-radius:6px;padding:.3rem .7rem;font-size:.78rem;font-weight:600;cursor:pointer">{{ __('panel::messages.cancel') }}</button>
                            @else
                                <button wire:click="editUser({{ $user->id }})" style="background:var(--panel-content-bg,#f1f5f9);color:var(--panel-text-primary,#475569);border:none;border-radius:6px;padding:.3rem .65rem;font-size:.78rem;font-weight:500;cursor:pointer;margin-right:.25rem">{{ __('panel::messages.edit') }}</button>
                                <button wire:click="confirmDelete({{ $user->id }})" style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:.3rem .65rem;font-size:.78rem;font-weight:500;cursor:pointer">{{ __('panel::messages.delete') }}</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="widget-table-empty">{{ __('panel::messages.no_users_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div style="padding:.75rem 1.25rem;border-top:1px solid var(--panel-card-border,#f1f5f9)">
            {{ $users->links() }}
        </div>
    @endif
</div>

@if ($showForm)
<div class="panel-section-card">
    <div class="panel-section-header">
        {{ $editingId ? __('panel::messages.edit_user') : __('panel::messages.new_user') }}
    </div>
    <div class="panel-section-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:var(--panel-text-muted,#64748b);margin-bottom:.35rem">{{ __('panel::messages.name') }}</label>
                <input type="text" wire:model="formName" placeholder="{{ __('panel::messages.full_name') }}"
                    style="width:100%;padding:.55rem .75rem;border:1px solid {{ $errors->has('formName') ? '#dc2626' : 'var(--panel-card-border,#e2e8f0)' }};border-radius:var(--panel-radius,8px);font-size:.875rem;color:var(--panel-text-primary,#334155);background:var(--panel-card-bg,#fff);outline:none">
                @error('formName') <span style="font-size:.78rem;color:#dc2626;margin-top:.25rem;display:block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:var(--panel-text-muted,#64748b);margin-bottom:.35rem">{{ __('panel::messages.email') }}</label>
                <input type="email" wire:model="formEmail" placeholder="user@example.com"
                    style="width:100%;padding:.55rem .75rem;border:1px solid {{ $errors->has('formEmail') ? '#dc2626' : 'var(--panel-card-border,#e2e8f0)' }};border-radius:var(--panel-radius,8px);font-size:.875rem;color:var(--panel-text-primary,#334155);background:var(--panel-card-bg,#fff);outline:none">
                @error('formEmail') <span style="font-size:.78rem;color:#dc2626;margin-top:.25rem;display:block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div style="margin-bottom:1.25rem;max-width:50%">
            <label style="display:block;font-size:.8rem;font-weight:600;color:var(--panel-text-muted,#64748b);margin-bottom:.35rem">
                {{ $editingId ? __('panel::messages.password_keep_current') : __('panel::messages.password') }}
            </label>
            <input type="password" wire:model="formPassword" placeholder="{{ __('panel::messages.minimum_characters') }}"
                style="width:100%;padding:.55rem .75rem;border:1px solid {{ $errors->has('formPassword') ? '#dc2626' : 'var(--panel-card-border,#e2e8f0)' }};border-radius:var(--panel-radius,8px);font-size:.875rem;color:var(--panel-text-primary,#334155);background:var(--panel-card-bg,#fff);outline:none">
            @error('formPassword') <span style="font-size:.78rem;color:#dc2626;margin-top:.25rem;display:block">{{ $message }}</span> @enderror
        </div>
        <div style="display:flex;gap:.75rem">
            <button wire:click="saveUser" style="background:var(--panel-primary,#4f46e5);color:#fff;border:none;border-radius:var(--panel-radius,8px);padding:.55rem 1.25rem;font-size:.875rem;font-weight:600;cursor:pointer">
                {{ $editingId ? __('panel::messages.save_changes') : __('panel::messages.create_user') }}
            </button>
            <button wire:click="cancelForm" style="background:var(--panel-content-bg,#f1f5f9);color:var(--panel-text-muted,#64748b);border:none;border-radius:var(--panel-radius,8px);padding:.55rem 1.25rem;font-size:.875rem;font-weight:500;cursor:pointer">
                {{ __('panel::messages.cancel') }}
            </button>
        </div>
    </div>
</div>
@endif
</div>
