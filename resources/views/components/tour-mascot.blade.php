@php
    $tourMascotFrames = [
        'talk1' => 'images/mascot/tour-parle-1.webp',
        'talk2' => 'images/mascot/tour-parle-2.webp',
        'closed' => 'images/mascot/tour-ferme.webp',
        'look' => 'images/mascot/tour-regard.webp',
    ];
    $tourMascotAvailable = cache()->remember('tour-mascot.assets-present', 60, function () use ($tourMascotFrames) {
        foreach ($tourMascotFrames as $path) {
            if (!file_exists(public_path($path))) return false;
        }
        return true;
    });
@endphp

@if($tourMascotAvailable)
    <div class="tour-mascot" aria-hidden="true">
        <img data-tour-frame="talk1" src="{{ asset($tourMascotFrames['talk1']) }}" alt="" decoding="async">
        <img data-tour-frame="talk2" src="{{ asset($tourMascotFrames['talk2']) }}" alt="" decoding="async">
        <img data-tour-frame="closed" src="{{ asset($tourMascotFrames['closed']) }}" alt="" decoding="async">
        <img data-tour-frame="look" src="{{ asset($tourMascotFrames['look']) }}" alt="" decoding="async">
    </div>
@endif
