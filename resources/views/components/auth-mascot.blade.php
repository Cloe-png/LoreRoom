@props([
    'frame1' => 'images/mascot/inscription-1.png',
    'frame2' => 'images/mascot/inscription-2.png',
    'size' => 450,
])

@php
    $assetsAvailable = cache()->remember('auth-mascot.assets-present', 60, function () use ($frame1, $frame2) {
        return file_exists(public_path($frame1)) && file_exists(public_path($frame2));
    });
@endphp

@if($assetsAvailable)
    <div class="auth-mascot" aria-hidden="true" style="--mascot-width: {{ $size }}px">
        <img class="f1" src="{{ asset($frame1) }}" alt="" width="{{ $size }}" height="{{ $size }}" decoding="async">
        <img class="f2" src="{{ asset($frame2) }}" alt="" width="{{ $size }}" height="{{ $size }}" decoding="async">
    </div>
@endif
