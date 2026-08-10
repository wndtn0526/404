{{-- 꺾은선 차트 — Figma GPRO_PORTFOLIO node 1002-88736 실측.
     선 Warm gray/800 · 값이 있는 달까지만 그린다 · 강조한 달에 점(바깥 16 · 안쪽 8)

     props
       labels    : x 축 라벨
       values    : 값 배열. null 이면 그 달부터는 선을 잇지 않는다.
       max       : y 축 최댓값 (플롯 맨 위)
       highlight : 점을 찍고 칸을 강조할 달 번호(0부터)
       yLabels   : x-chart.xy 로 그대로 넘긴다 --}}
@props([
    'labels' => [],
    'values' => [],
    'max' => 1,
    'highlight' => null,
    'yLabels' => [],
])

@php
    $cols = max(count($labels), 1);
    $cellW = 637 / $cols;

    // 점은 칸 가운데에 찍힌다 (원본 1월 x428 = 401 + 53/2)
    $points = [];
    foreach ($values as $i => $v) {
        if ($v === null) {
            continue;
        }
        $points[$i] = [round(($i + 0.5) * $cellW, 2), round(200 - ($v / $max) * 200, 2)];
    }
    $path = implode(' ', array_map(fn ($p) => $p[0].','.$p[1], $points));
    $dot = $highlight !== null ? ($points[$highlight] ?? null) : null;
@endphp

<x-chart.xy :labels="$labels" :y-labels="$yLabels" :highlight="$highlight" {{ $attributes }}>
    @if ($path)
        <polyline points="{{ $path }}" class="stroke-warm-gray-800" stroke-width="1.5"
                  stroke-linejoin="round" stroke-linecap="round" />
    @endif

    @if ($dot)
        {{-- 바깥 원은 옅게 깔고 안쪽만 진하게 — 원본 16 / 8 --}}
        <circle cx="{{ $dot[0] }}" cy="{{ $dot[1] }}" r="8" class="fill-warm-gray-800/15" />
        <circle cx="{{ $dot[0] }}" cy="{{ $dot[1] }}" r="4" class="fill-warm-gray-800" />
    @endif
</x-chart.xy>
