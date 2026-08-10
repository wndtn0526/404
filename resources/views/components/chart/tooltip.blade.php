{{-- 차트 말풍선 — Figma GPRO_PORTFOLIO node 1002-88769 실측:
     면 Warm gray/800 · 반경 6 · 패딩 10 · 글자 11 흰색 (값만 Bold) · 그림자 Elevation/E200.

     ⚠️ 원본은 마우스를 올린 지점에 뜬다. 여기서는 정적 화면이라 강조한 달 옆에 세워 뒀다.
        놓는 자리는 쓰는 쪽에서 정한다(absolute + 위치 클래스).

     props
       title : 윗줄 (예: 2021.09)
       label : 아랫줄 앞 글자 (예: 전체)
       value : 아랫줄 숫자 --}}
@props(['title' => null, 'label' => null, 'value' => null])

<div {{ $attributes->class('pointer-events-none rounded-lg bg-warm-gray-800 p-2.5 shadow-elevation-lg') }}>
    @if ($title)
        <p class="whitespace-nowrap text-caption-2 leading-4 text-white tabular-nums">{{ $title }}</p>
    @endif
    <p class="whitespace-nowrap pt-1 text-caption-2 leading-[17px] text-white">
        @if ($label)<span>{{ $label }} </span>@endif
        <span class="font-bold tabular-nums">{{ $value }}</span>
    </p>
</div>
