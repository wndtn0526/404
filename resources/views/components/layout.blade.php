@props([
    'title' => null,
    // 기본은 "제목 · 앱이름". 디자인 시스템처럼 앱 밖으로 나가는 화면은 제목만 쓴다.
    'bareTitle' => false,
])

<!DOCTYPE html>
<html lang="ko" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ? ($bareTitle ? $title : $title . ' · ' . config('app.name')) : config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full">
        {{ $slot }}

        {{-- 확인 다이얼로그·토스트는 화면마다 마크업을 두지 않고 여기 한 번만 심는다.
             어디서든 이벤트로 부른다:
               window.dispatchEvent(new CustomEvent('confirm', { detail: { title, message, onConfirm } }))
               window.dispatchEvent(new CustomEvent('toast', { detail: { message } }))
             ⚠️ 심어 두지 않으면 이벤트를 아무도 안 듣는다. 그동안 두 컴포넌트가
                여기 없어서 조용히 아무 일도 하지 않았다. --}}
        <x-confirm />
        <x-toast />

        {{-- 컴포넌트가 @push('scripts') 로 올린 스크립트가 여기 쌓인다.
             ⚠️ 이 스택이 없으면 그 스크립트가 조용히 버려진다. x-file-dropzone 과
                x-datepicker 가 Alpine.data 를 이 방식으로 등록하는데, 스택이 없어서
                x-data="dsFileDropzone(...)" 만 나가고 정의는 안 나가고 있었다.
             ⚠️ 위치가 중요하다. Alpine 은 @vite 가 head 에 넣는 module 스크립트라 문서 파싱
                뒤에 실행된다. 여기 있는 일반 <script> 는 파싱 중에 돌아 alpine:init 리스너를
                먼저 등록한다. head 로 올리면 리스너가 늦어 등록이 씹힌다. --}}
        @stack('scripts')

        @livewireScripts
    </body>
</html>
