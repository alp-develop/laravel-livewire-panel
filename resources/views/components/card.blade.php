<div {{ $attributes->merge(['class' => $theme->classes('card', 'root')]) }}>
    @if($title)
    <div class="{{ $theme->classes('card', 'header') }}">{{ $title }}</div>
    @endif
    <div class="{{ $theme->classes('card', 'body') }}">
        {{ $slot }}
    </div>
    @if(isset($footer))
    <div class="{{ $theme->classes('card', 'footer') }}">{{ $footer }}</div>
    @endif
</div>
