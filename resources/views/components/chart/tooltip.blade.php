{{-- 차트 말풍선 — Figma GPRO_PORTFOLIO node 1002-88769 실측:
     면 Warm gray/800 · 반경 6 · 패딩 10 · 글자 11 흰색 (값만 Bold) · 그림자 Elevation/E200.

     놓는 자리는 쓰는 쪽에서 정한다(absolute + 위치 클래스).
     x-chart.xy · x-chart.donut 이 마우스를 올린 지점에 이걸 띄운다.

     props (정적으로 쓸 때)
       title : 윗줄 (예: 2021.09)
       label : 아랫줄 앞 글자 (예: 전체)
       value : 아랫줄 숫자
     슬롯을 주면 props 대신 그 내용을 쓴다 — 호버처럼 문구가 바뀌는 자리에 쓴다. --}}
@props(['title' => null, 'label' => null, 'value' => null])

<div {{ $attributes->class('pointer-events-none rounded-lg bg-warm-gray-800 p-2.5 shadow-elevation-lg') }}>
    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        @if ($title)
            <p class="whitespace-nowrap text-caption-2 leading-4 text-white tabular-nums">{{ $title }}</p>
        @endif
        <p class="whitespace-nowrap pt-1 text-caption-2 leading-[17px] text-white">
            @if ($label)<span>{{ $label }} </span>@endif
            <span class="font-bold tabular-nums">{{ $value }}</span>
        </p>
    @endif
</div>
