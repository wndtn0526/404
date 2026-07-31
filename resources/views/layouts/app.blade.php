{{-- Livewire 페이지 컴포넌트용 레이아웃. 실제 문서 셸은 x-layout 한 곳에만 둔다. --}}
<x-layout :title="$title ?? null">
    {{ $slot }}
</x-layout>
