{{-- 차트 격자 — 꺾은선·막대가 공유하는 축과 눈금.
     Figma GPRO_PORTFOLIO node 1002-88736 (거래처·개인 비용 추이) 실측:
       플롯 637x200 · 세로 눈금 13줄(칸 12개 · 간격 53) · 가로선 위·중간·아래
       눈금선 Warm gray/050 · y 라벨 12 (칸 41, 오른쪽 정렬) · x 라벨 12 (칸 가운데)
       플롯 왼쪽 여백 51 (401 - 350) · x 라벨은 축에서 8 아래
       강조 칸은 그 칸 전체를 Warm gray/050 으로 채운다

     슬롯은 SVG 좌표(0 0 637 200 · y 는 위가 0)로 그린다. 축을 두 번 그리지 않으려고
     선·막대가 이 안으로 들어온다.

     ⚠️ 원본은 이 화면 하나뿐이라 DS 에 차트 규격이 따로 없다. 여기 값들이 사실상
        첫 정의다. 다른 차트가 생기면 이 컴포넌트를 같이 고친다.

     props
       labels    : x 축 라벨 배열. 칸 수가 곧 이 배열의 길이다.
       yLabels   : [['at' => 0~1(아래에서부터 비율), 'text' => '10,000k'], …]
       gridLines : 가로 눈금선을 그릴 비율들. 기본은 위·중간·아래.
       highlight : 강조할 칸 번호(0부터). null 이면 안 그린다. --}}
@props([
    'labels' => [],
    'yLabels' => [],
    'gridLines' => [0, 0.4, 1],
    'highlight' => null,
])

@php
    $cols = max(count($labels), 1);
    $cellW = 637 / $cols;
@endphp

<div {{ $attributes->class('relative pl-[51px]') }}>
    {{-- y 라벨 — 눈금선 높이에 세로 가운데를 맞춘다(줄 16의 절반 8) --}}
    @foreach ($yLabels as $y)
        <span class="absolute left-0 w-[41px] text-right text-caption-1 leading-4 text-label-alternative tabular-nums"
              style="bottom: calc({{ $y['at'] * 100 }}% - 8px)">{{ $y['text'] }}</span>
    @endforeach

    <svg class="block h-auto w-full" viewBox="0 0 637 200" fill="none" aria-hidden="true">
        {{-- 강조 칸 --}}
        @if ($highlight !== null)
            <rect x="{{ round($highlight * $cellW, 2) }}" y="0" width="{{ round($cellW, 2) }}" height="200"
                  class="fill-warm-gray-50" />
        @endif

        {{-- 세로 눈금 — 칸 경계라 칸 수보다 하나 많다 --}}
        @for ($i = 0; $i <= $cols; $i++)
            <line x1="{{ round($i * $cellW, 2) }}" y1="0" x2="{{ round($i * $cellW, 2) }}" y2="200"
                  class="stroke-warm-gray-50" stroke-width="1" />
        @endfor

        {{-- 가로 눈금 --}}
        @foreach ($gridLines as $at)
            <line x1="0" y1="{{ round(200 - $at * 200, 2) }}" x2="637" y2="{{ round(200 - $at * 200, 2) }}"
                  class="stroke-warm-gray-50" stroke-width="1" />
        @endforeach

        {{ $slot }}
    </svg>

    {{-- x 라벨 — 칸 가운데. 축에서 8 아래(원본 581 → 589) --}}
    <div class="flex pt-2">
        @foreach ($labels as $label)
            <span class="min-w-0 flex-1 text-center text-caption-1 leading-[17px] text-label-alternative">{{ $label }}</span>
        @endforeach
    </div>
</div>
