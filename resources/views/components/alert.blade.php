<div {{ $attributes->merge(['class' => trim($theme->classes('alert', 'base') . ' ' . $theme->classes('alert', $variant) . ($dismissible ? ' ' . $theme->classes('alert', 'dismissible') : '')), 'style' => 'display:flex;align-items:flex-start;gap:.5rem']) }} role="alert">
    <div style="flex:1">{{ $slot }}</div>
    @if($dismissible)
    <button type="button" class="{{ $theme->classes('alert', 'close') }}" {!! $theme->classes('alert', 'close_attrs') !!} style="background:none;border:none;cursor:pointer;padding:0;line-height:1;font-size:1.2rem;opacity:.7;flex-shrink:0">&times;</button>
    @endif
</div>
