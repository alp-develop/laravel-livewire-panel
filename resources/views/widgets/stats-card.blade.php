<div @if($pollSeconds > 0) wire:poll="{{ $pollSeconds * 1000 }}ms" @endif style="height:100%">
    <div style="background:var(--panel-card-bg,#fff);border-radius:10px;padding:1.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.07);border:1px solid var(--panel-card-border,#e8edf2);height:100%;display:flex;flex-direction:column;justify-content:space-between">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem">
            <div style="min-width:0;flex:1">
                <p style="margin:0 0 0.5rem;font-size:0.75rem;color:var(--panel-text-muted,#6b7280);font-weight:700;text-transform:uppercase;letter-spacing:0.08em">{{ $title }}</p>
                <h3 style="margin:0;font-size:2.1rem;font-weight:800;color:var(--panel-text-primary,#0f172a);letter-spacing:-0.02em;line-height:1">{{ $value }}</h3>
            </div>
            <div style="flex-shrink:0;width:54px;height:54px;border-radius:12px;background:var(--panel-primary,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 4px 12px rgba(79,70,229,0.35)">
                <x-panel::icon :name="$icon" size="26" />
            </div>
        </div>
        @if ($trend)
            <div style="margin-top:1rem;padding-top:0.875rem;border-top:1px solid var(--panel-card-border,#f3f4f6)">
                <span style="display:inline-flex;align-items:center;padding:0.25rem 0.65rem;border-radius:999px;font-size:0.78rem;font-weight:700;color:#fff;
                    background:{{ $trendType === 'up' ? '#16a34a' : ($trendType === 'down' ? '#dc2626' : '#4b5563') }}">
                    {{ $trend }}
                </span>
            </div>
        @endif
    </div>
</div>
