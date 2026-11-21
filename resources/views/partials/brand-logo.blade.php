@php
    $size = $size ?? 64;
    $class = $class ?? '';
@endphp

<img
    src="{{ asset('images/brand/loreroom.png') }}"
    alt="LoreRoom"
    style="height: {{ $size }}px; width: auto;"
    class="{{ trim('brand-logo ' . $class) }}"
>
