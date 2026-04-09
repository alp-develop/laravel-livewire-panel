<div @if($pollSeconds > 0) wire:poll="{{ $pollSeconds * 1000 }}ms" @endif>
    <div style="background:var(--panel-card-bg,#fff);border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,0.08);border:1px solid var(--panel-card-border,#e8edf2);overflow:hidden;min-width:0">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--panel-card-border,#f3f4f6);font-weight:600;font-size:0.9rem;color:var(--panel-text-primary,#111827)">{{ $title }}</div>
        <div style="padding:1.25rem;min-width:0">
            <div
                x-data="panelChart"
                data-ctype="{{ $type }}"
                data-cfg="{{ json_encode(['labels' => $labels, 'datasets' => $datasets]) }}"
                style="position:relative;height:{{ $height }}px;width:100%;overflow:hidden"
            >
                <canvas style="display:block;max-width:100%"></canvas>
            </div>
        </div>
    </div>
</div>
