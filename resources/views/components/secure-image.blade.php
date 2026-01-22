@props(['path', 'alt' => '', 'class' => ''])

@php
    $imageService = app(App\Services\ImageService::class);
    $url = $imageService->getUrl($path);
@endphp

<img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
