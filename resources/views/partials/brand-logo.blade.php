@php
    $size = $size ?? 64;
    $class = $class ?? '';
@endphp

<img
    src="{{ asset('loreroom.png') }}"
    alt="LoreRoom"
    style="height: {{ $size }}px; width: auto;"
    class="{{ trim('brand-logo ' . $class) }}"
>
