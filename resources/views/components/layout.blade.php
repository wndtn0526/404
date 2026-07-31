@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="ko" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ? $title . ' · ' . config('app.name') : config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full">
        {{ $slot }}

        @livewireScripts
    </body>
</html>
