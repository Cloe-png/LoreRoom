@php
    $flowerAsset = collect(['png', 'webp', 'svg'])->map(fn ($ext) => "images/memorial/fleur.$ext")->first(fn ($path) => file_exists(public_path($path)));
@endphp
@if($flowerAsset)
    <img src="{{ asset($flowerAsset) }}" alt="Fleur" width="80" height="80">
@else
    <svg viewBox="0 0 100 140" width="80" height="112" role="img" aria-label="Fleur">
        <g fill="none" stroke="currentColor" stroke-width="3">
            <path d="M50 72 C50 95 52 117 50 138" />
            <path d="M50 108 C31 95 26 111 50 116 M50 121 C66 104 79 115 50 128" />
            <g fill="currentColor"><ellipse cx="50" cy="34" rx="10" ry="19"/><ellipse cx="50" cy="34" rx="10" ry="19" transform="rotate(72 50 50)"/><ellipse cx="50" cy="34" rx="10" ry="19" transform="rotate(144 50 50)"/><ellipse cx="50" cy="34" rx="10" ry="19" transform="rotate(216 50 50)"/><ellipse cx="50" cy="34" rx="10" ry="19" transform="rotate(288 50 50)"/><circle cx="50" cy="50" r="8"/></g>
        </g>
    </svg>
@endif
