@extends('manage.layout')

@section('title', __('app.chapters.page_title'))
@section('header', __('app.chapters.new_title'))

@section('content')
    @include('manage.chapters.form', [
        'chapter' => null,
        'world' => $world,
        'characters' => $characters,
        'places' => $places,
        'action' => route('manage.chapters.store'),
        'method' => 'POST',
        'submitLabel' => __('app.chapters.create'),
        'selectedCharacterIds' => collect(old('character_ids', []))->map(fn ($value) => (int) $value)->all(),
        'selectedPlaceIds' => collect(old('place_ids', []))->map(fn ($value) => (int) $value)->all(),
        'characterDetailsMap' => collect(old('character_details', []))->mapWithKeys(function ($row, $key) {
            return [(string) $key => [
                'outfit_source' => (string) ($row['outfit_source'] ?? ''),
                'outfit' => (string) ($row['outfit'] ?? ''),
                'outfit_image_path' => (string) ($row['outfit_image_path'] ?? ''),
                'emotion' => (string) ($row['emotion'] ?? ''),
            ]];
        })->all(),
        'dateValue' => old('chapter_date'),
        'summaryValue' => old('summary'),
        'descriptionValue' => old('description'),
        'titleValue' => old('title'),
    ])
@endsection
