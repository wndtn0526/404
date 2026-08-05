@props([
    'padding' => 'md',       // none | sm | md | lg
    'elevation' => 'sm',     // none | xs | sm | md | lg
    'as' => 'div',
])

@php
    $paddings = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];

    $elevations = [
        'none' => 'border border-line-solid-normal',
        'xs' => 'shadow-elevation-xs',
        'sm' => 'shadow-elevation-sm',
        'md' => 'shadow-elevation-md',
        'lg' => 'shadow-elevation-lg',
    ];

    $classes = implode(' ', [
        'bg-background-elevated-normal rounded-none',
        $paddings[$padding] ?? $paddings['md'],
        $elevations[$elevation] ?? $elevations['sm'],
    ]);
@endphp

<{{ $as }} {{ $attributes->class($classes) }}>
    {{ $slot }}
</{{ $as }}>
