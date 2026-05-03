<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:5rem 2rem;text-align:center;min-height:60vh;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
    <div style="font-size:8rem;font-weight:900;line-height:1;color:var(--panel-primary,#4f46e5);letter-spacing:-6px;margin-bottom:.25rem">404</div>
    <h1 style="margin:.5rem 0 .75rem;font-size:1.75rem;font-weight:700;color:#0f172a;letter-spacing:-.025em">{{ __('panel::messages.not_found_title') }}</h1>
    <p style="margin:0 0 2.5rem;font-size:1rem;color:#64748b;max-width:400px;line-height:1.7">{{ __('panel::messages.not_found_message') }}</p>
    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;justify-content:center">
        <button onclick="history.back()" style="padding:.75rem 2rem;background:var(--panel-primary,#4f46e5);color:#fff;font-size:.9375rem;font-weight:600;border-radius:var(--panel-radius,8px);border:none;cursor:pointer;letter-spacing:.01em;box-shadow:0 2px 8px color-mix(in srgb,var(--panel-primary,#4f46e5) 35%,transparent)">
            {{ __('panel::messages.go_back') }}
        </button>
        @if(!empty($panelId) && \Illuminate\Support\Facades\Route::has("panel.{$panelId}.home"))
        <a href="{{ panel_route($panelId, 'home') }}" style="padding:.75rem 2rem;background:transparent;color:var(--panel-primary,#4f46e5);font-size:.9375rem;font-weight:600;border-radius:var(--panel-radius,8px);text-decoration:none;border:2px solid var(--panel-primary,#4f46e5);letter-spacing:.01em">
            {{ __('panel::messages.back_to_home') }}
        </a>
        @endif
    </div>
</div>
