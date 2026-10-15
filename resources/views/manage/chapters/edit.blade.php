@extends('manage.layout')

@section('title', __('app.chapters.page_title'))
@section('header', __('app.chapters.edit_title'))

@section('content')
    @php
        $existingCharacterDetails = collect($chapter->character_feelings ?? [])->mapWithKeys(function ($row) {
            return [(string) ($row['character_id'] ?? 0) => [
                'outfit_source' => (string) ($row['outfit_source'] ?? ''),
                'outfit' => (string) ($row['outfit'] ?? ''),
                'outfit_image_path' => (string) ($row['outfit_image_path'] ?? ''),
                'emotion' => (string) ($row['emotion'] ?? ''),
            ]];
        })->all();

        $characterDetailsMap = collect(old('character_details', $existingCharacterDetails))->mapWithKeys(function ($row, $key) {
            return [(string) $key => [
                'outfit_source' => (string) ($row['outfit_source'] ?? ''),
                'outfit' => (string) ($row['outfit'] ?? ''),
                'outfit_image_path' => (string) ($row['outfit_image_path'] ?? ''),
                'emotion' => (string) ($row['emotion'] ?? ''),
            ]];
        })->all();
    @endphp

    @include('manage.chapters.form', [
        'chapter' => $chapter,
        'world' => $world,
        'characters' => $characters,
        'places' => $places,
        'action' => route('manage.chapters.update', $chapter),
        'method' => 'PUT',
        'submitLabel' => __('app.chapters.save'),
        'selectedCharacterIds' => collect(old('character_ids', $chapter->characters->pluck('id')->all()))->map(fn ($value) => (int) $value)->all(),
        'selectedPlaceIds' => collect(old('place_ids', $chapter->places->pluck('id')->all()))->map(fn ($value) => (int) $value)->all(),
        'characterDetailsMap' => $characterDetailsMap,
        'dateValue' => old('chapter_date', optional($chapter->chapter_date)->format('Y-m-d')),
        'summaryValue' => old('summary', $chapter->summary),
        'descriptionValue' => old('description', $chapter->description),
        'titleValue' => old('title', $chapter->title),
    ])
@endsection
