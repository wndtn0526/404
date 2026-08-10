{{-- 차트 격자 — 꺾은선·막대가 공유하는 축과 눈금.
     Figma GPRO_PORTFOLIO node 1002-88736 (거래처·개인 비용 추이) 실측:
       플롯 637x200 · 세로 눈금 13줄(칸 12개 · 간격 53) · 가로선 위·중간·아래
       눈금선 Warm gray/050 · y 라벨 12 (칸 41, 오른쪽 정렬) · x 라벨 12 (칸 가운데)
       플롯 왼쪽 여백 51 (401 - 350) · x 라벨은 축에서 8 아래
       마우스를 올린 칸은 그 칸 전체를 Warm gray/050 으로 채우고 말풍선을 띄운다

     슬롯은 SVG 좌표(0 0 637 200 · y 는 위가 0)로 그린다. 축을 두 번 그리지 않으려고
     선·막대가 이 안으로 들어온다.

     ★ 호버 상태는 여기서 갖는다. 안에 그리는 쪽(line·bars)이 값을 넣어 준다.
         hover : 강조할 칸 번호 (null 이면 강조 없음)
         tip   : { title, label, value, x, y } — x·y 는 플롯 기준 퍼센트
       슬롯 안에서 마우스 이벤트로 이 둘을 세팅하면 띠와 말풍선이 따라온다.

     ⚠️ 원본은 이 화면 하나뿐이라 DS 에 차트 규격이 따로 없다. 여기 값들이 사실상
        첫 정의다. 다른 차트가 생기면 이 컴포넌트를 같이 고친다.

     props
       labels    : x 축 라벨 배열. 칸 수가 곧 이 배열의 길이다.
       yLabels   : [['at' => 0~1(아래에서부터 비율), 'text' => '10,000k'], …]
       gridLines : 가로 눈금선을 그릴 비율들. 기본은 위·중간·아래. --}}
@props([
    'labels' => [],
    'yLabels' => [],
    'gridLines' => [0, 0.4, 1],
])

@php
    $cols = max(count($labels), 1);
    $cellW = 637 / $cols;
@endphp

<div {{ $attributes->class('relative pl-[51px]') }} x-data="{ hover: null, tip: null }">
    {{-- y 라벨 — 눈금선 높이에 세로 가운데를 맞춘다(줄 16의 절반 8) --}}
    @foreach ($yLabels as $y)
        <span class="absolute left-0 w-[41px] text-right text-caption-1 leading-4 text-label-alternative tabular-nums"
              style="bottom: calc({{ $y['at'] * 100 }}% - 8px)">{{ $y['text'] }}</span>
    @endforeach

    {{-- 플롯 상자 — 말풍선을 SVG 와 같은 좌표계에 얹으려고 따로 감싼다 --}}
    <div class="relative">
        <svg class="block h-auto w-full" viewBox="0 0 637 200" fill="none" aria-hidden="true">
            {{-- 마우스를 올린 칸 --}}
            <rect x="0" y="0" width="{{ round($cellW, 2) }}" height="200" class="fill-warm-gray-50"
                  x-bind:x="hover === null ? -1000 : hover * {{ round($cellW, 4) }}" />

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

        {{-- 말풍선 — 올린 지점 오른쪽에 선다. 오른쪽 끝 칸에서는 왼쪽으로 넘긴다. --}}
        {{-- ⚠️ 정적 class 에 hidden 을 두면 안 된다. x-bind:class 는 정적 클래스를 지우지
             못해서 tip 이 있어도 hidden 이 남는다. 처음 숨기는 건 x-cloak 이 맡는다. --}}
        <x-chart.tooltip class="absolute z-10 -translate-y-1/2"
                         x-cloak
                         x-bind:class="tip ? 'block' : 'hidden'"
                         x-bind:style="tip ? `left:${tip.x}%; top:${tip.y}%; transform:translate(${tip.x > 70 ? 'calc(-100% - 12px)' : '12px'}, -50%)` : ''">
            <p class="whitespace-nowrap text-caption-2 leading-4 text-white tabular-nums" x-text="tip?.title"></p>
            <p class="whitespace-nowrap pt-1 text-caption-2 leading-[17px] text-white">
                <span x-bind:class="{ 'hidden': ! tip?.label }" x-text="(tip?.label ?? '') + ' '"></span><span
                    class="font-bold tabular-nums" x-text="tip?.value"></span>
            </p>
        </x-chart.tooltip>
    </div>

    {{-- x 라벨 — 칸 가운데. 축에서 8 아래(원본 581 → 589) --}}
    <div class="flex pt-2">
        @foreach ($labels as $label)
            <span class="min-w-0 flex-1 text-center text-caption-1 leading-[17px] text-label-alternative">{{ $label }}</span>
        @endforeach
    </div>
</div>
