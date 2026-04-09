<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => trim($theme->classes('button', 'base') . ' ' . $theme->classes('button', $size) . ' ' . $theme->classes('button', $variant))]) }}
>{{ $slot }}</button>
