{{-- 꺾은선 차트 — Figma GPRO_PORTFOLIO node 1002-88736 실측.
     선 Warm gray/800 · 값이 있는 달까지만 그린다 · 마우스를 올린 달에 점(바깥 16 · 안쪽 8)

     칸마다 투명한 사각형을 깔아 호버를 받는다. 선 위에만 반응하면 잡기 어렵다.

     props
       labels  : x 축 라벨
       values  : 값 배열. null 이면 그 달은 점도 말풍선도 없다.
       max     : y 축 최댓값 (플롯 맨 위)
       unit    : 말풍선 값 뒤에 붙일 단위 (없으면 숫자만)
       tipLabel: 말풍선 아랫줄 앞 글자 (예: 전체)
       yLabels : x-chart.xy 로 그대로 넘긴다 --}}
@props([
    'labels' => [],
    'values' => [],
    'max' => 1,
    'unit' => '',
    'tipLabel' => null,
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

    // Alpine 이 쓸 표: 칸 번호 → 점 좌표(퍼센트)와 말풍선 문구
    $tips = [];
    foreach ($points as $i => $p) {
        $tips[$i] = [
            'title' => $labels[$i] ?? '',
            'label' => $tipLabel,
            'value' => number_format($values[$i]).$unit,
            'x' => round($p[0] / 637 * 100, 3),
            'y' => round($p[1] / 200 * 100, 3),
        ];
    }
@endphp

<x-chart.xy :labels="$labels" :y-labels="$yLabels" {{ $attributes }}>
    @if ($path)
        <polyline points="{{ $path }}" class="stroke-warm-gray-800" stroke-width="1.5"
                  stroke-linejoin="round" stroke-linecap="round" />
    @endif

    {{-- 마우스를 올린 달의 점 — 바깥은 옅게 깔고 안쪽만 진하게(원본 16 / 8) --}}
    <g x-bind:class="{ 'hidden': tip === null }">
        <circle cx="0" cy="0" r="8" class="fill-warm-gray-800/15"
                x-bind:cx="tip ? tip.x / 100 * 637 : 0" x-bind:cy="tip ? tip.y / 100 * 200 : 0" />
        <circle cx="0" cy="0" r="4" class="fill-warm-gray-800"
                x-bind:cx="tip ? tip.x / 100 * 637 : 0" x-bind:cy="tip ? tip.y / 100 * 200 : 0" />
    </g>

    {{-- 호버 판 — 칸 전체를 덮는다. 값이 없는 달은 띠만 뜨고 말풍선은 없다. --}}
    @foreach ($labels as $i => $label)
        <rect x="{{ round($i * $cellW, 2) }}" y="0" width="{{ round($cellW, 2) }}" height="200"
              fill="transparent"
              @mouseenter="hover = {{ $i }}; tip = {{ isset($tips[$i]) ? Js::from($tips[$i]) : 'null' }}"
              @mouseleave="hover = null; tip = null" />
    @endforeach
</x-chart.xy>
