<div x-data="{ open: false }" x-on:click.outside="open = false" @keydown.escape.window="open = false" style="position:relative;display:inline-block">
    <button type="button" x-on:click="open = !open" style="display:inline-flex;align-items:center;gap:4px;background:none;border:1px solid var(--panel-card-border,#e2e8f0);border-radius:6px;padding:5px 10px;cursor:pointer;font-size:.8rem;font-weight:600;color:var(--panel-text-primary,#334155)" title="{{ __('panel::messages.language') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
        <span style="text-transform:uppercase;letter-spacing:.03em">{{ $current }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="12" height="12" style="opacity:.6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
    </button>
    <div x-show="open" x-transition x-cloak style="position:absolute;right:0;top:100%;margin-top:4px;min-width:140px;background:var(--panel-card-bg,#fff);border:1px solid var(--panel-card-border,#e2e8f0);border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:50;overflow:hidden">
        @foreach ($available as $code => $label)
            <a href="{{ route('panel.locale.switch', $code) }}" style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:8px 14px;text-decoration:none;color:var(--panel-text-primary,#334155);font-size:.85rem;{{ $code === $current ? 'font-weight:600;background:var(--panel-content-bg,#f8fafc)' : '' }}" onmouseover="this.style.background='var(--panel-content-bg,#f1f5f9)'" onmouseout="this.style.background='{{ $code === $current ? 'var(--panel-content-bg,#f8fafc)' : 'transparent' }}'">
                <span>{{ $label }}</span>
                @if ($code === $current)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="14" height="14" style="color:var(--panel-primary,#4f46e5)"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
