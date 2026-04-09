@php
$legacy = [
    'x-mark' => 'xmark',
    'information-circle' => 'circle-info',
    'exclamation-triangle' => 'triangle-exclamation',
    'exclamation-circle' => 'circle-exclamation',
    'cog-6-tooth' => 'gear',
    'square-3-stack' => 'layer-group',
    'rectangle-stack' => 'layer-group',
    'arrow-trending-up' => 'arrow-trend-up',
    'arrow-trending-down' => 'arrow-trend-down',
    'currency-dollar' => 'dollar-sign',
    'shopping-cart' => 'cart-shopping',
    'arrow-right-on-rectangle' => 'right-from-bracket',
    'bars-3' => 'bars',
    'arrow-top-right-on-square' => 'arrow-up-right-from-square',
    'lock-closed' => 'lock',
    'globe-alt' => 'globe',
    'home' => 'house',
];
$faName = $legacy[$name] ?? $name;
$faStyle = 'fa-solid';
$existingStyle = $attributes->get('style', '');
$sizeStyle = 'font-size:' . $size . 'px';
$fullStyle = $existingStyle ? $sizeStyle . ';' . $existingStyle : $sizeStyle;
@endphp
<i class="{{ trim($faStyle . ' fa-' . $faName . ' ' . $class) }}" style="{{ $fullStyle }}" {{ $attributes->except(['class', 'style']) }}></i>
