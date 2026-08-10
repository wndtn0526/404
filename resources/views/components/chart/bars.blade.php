{{-- 누적 막대 차트 — Figma GPRO_PORTFOLIO node 1002-88796 실측.
     막대 폭 30 · 칸 가운데 · 값이 0 인 달도 4 높이 토막을 남긴다(원본 30x4 · 반경 2).
     조각 색은 완성된 유틸리티 클래스로 받는다 — Tailwind 가 문자열로 훑기 때문이다.

     props
       labels    : x 축 라벨
       groups    : 달마다 조각 배열. [[['value' => 3000, 'class' => 'text-purple-900'], …], …]
                   빈 배열이면 그 달은 아무것도 안 그린다(원본 10~12월).
       max       : y 축 최댓값
       highlight : 강조할 달 번호
       yLabels   : x-chart.xy 로 그대로 넘긴다 --}}
@props([
    'labels' => [],
    'groups' => [],
    'max' => 1,
    'highlight' => null,
    'yLabels' => [],
])

@php
    $cols = max(count($labels), 1);
    $cellW = 637 / $cols;
    $barW = 30;
@endphp

<x-chart.xy :labels="$labels" :y-labels="$yLabels" :highlight="$highlight" {{ $attributes }}>
    @foreach ($groups as $i => $segments)
        @php
            $x = round(($i + 0.5) * $cellW - $barW / 2, 2);
            $total = collect($segments)->sum('value');
            $bottom = 200.0;
        @endphp

        @if ($total <= 0 && count($segments))
            {{-- 값이 없는 달 — 원본은 축 위에 4 높이 토막만 남긴다 --}}
            <rect x="{{ $x }}" y="196" width="{{ $barW }}" height="4" rx="2" class="fill-warm-gray-200" />
        @else
            @foreach ($segments as $seg)
                @php
                    $h = round(($seg['value'] / $max) * 200, 2);
                    $bottom -= $h;
                @endphp
                <g class="{{ $seg['class'] }}">
                    <rect x="{{ $x }}" y="{{ round($bottom, 2) }}" width="{{ $barW }}" height="{{ $h }}" fill="currentColor" />
                </g>
            @endforeach
        @endif
    @endforeach
</x-chart.xy>
