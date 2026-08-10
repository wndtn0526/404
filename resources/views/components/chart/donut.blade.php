{{-- 도넛 차트 — Figma GPRO_PORTFOLIO node 1002-88964 실측: 바깥 지름 160.
     조각 색은 완성된 유틸리티 클래스로 받는다.

     ⚠️ 원본은 도넛이 통 SVG 한 장으로 내려온다. 그 파일을 그대로 쓰면 값이 바뀌어도
        그림이 안 바뀐다 — 차트는 데이터에서 나와야 해서 각도로 다시 그렸다.
        아이콘·삽화였다면 내려받은 파일을 그대로 썼을 것이다.
     ⚠️ 안쪽 구멍 지름은 원본 이미지에서 눈으로 잰 값이다(테두리 34).

     조각마다 호버를 받아 오른쪽에 말풍선을 띄운다(원본이 '게임 제작 (33%) / 8,000' 이다).

     props
       slices : [['label' =>, 'value' =>, 'class' => 'text-purple-900'], …]
       amounts: 조각별 금액. 없으면 말풍선에 value(비율)를 그대로 보인다.
       unit   : 말풍선 값 뒤 단위 --}}
@props(['slices' => [], 'amounts' => [], 'unit' => ''])

@php
    $total = collect($slices)->sum('value') ?: 1;
    $r = 63;            // 테두리 한가운데 반지름 (바깥 80 · 안쪽 46)
    $circumference = 2 * M_PI * $r;
    $offset = 0.0;      // 12시부터 시계방향
@endphp

<div {{ $attributes->class('relative inline-block') }} x-data="{ tip: null }">
    <svg class="block size-40" viewBox="0 0 160 160" role="img" aria-label="비용 비율">
        @foreach ($slices as $i => $slice)
            @php
                $len = $circumference * ($slice['value'] / $total);
                $tip = [
                    'title' => ($slice['label'] ?? '').' ('.round($slice['value'] / $total * 100).'%)',
                    'value' => isset($amounts[$i]) ? number_format($amounts[$i]).$unit : $slice['value'].'%',
                ];
            @endphp
            <circle cx="80" cy="80" r="{{ $r }}" fill="none" stroke="currentColor" stroke-width="34"
                    stroke-dasharray="{{ round($len, 2) }} {{ round($circumference - $len, 2) }}"
                    stroke-dashoffset="{{ round(-$offset, 2) }}"
                    transform="rotate(-90 80 80)"
                    class="{{ $slice['class'] }} transition-opacity"
                    x-bind:class="tip && tip.title !== {{ Js::from($tip['title']) }} ? 'opacity-40' : ''"
                    @mouseenter="tip = {{ Js::from($tip) }}"
                    @mouseleave="tip = null" />
            @php $offset += $len; @endphp
        @endforeach
    </svg>

    {{-- 말풍선 — 도넛 오른쪽에 선다(원본도 오른쪽 테두리에 살짝 걸친다) --}}
    {{-- ⚠️ 정적 class 에 hidden 을 두지 않는다(x-bind:class 가 못 지운다). 처음은 x-cloak. --}}
    <x-chart.tooltip class="absolute left-[calc(100%-8px)] top-1/4 z-10 -translate-y-1/2"
                     x-cloak x-bind:class="tip ? 'block' : 'hidden'">
        <p class="whitespace-nowrap text-caption-2 leading-4 text-white" x-text="tip?.title"></p>
        <p class="whitespace-nowrap pt-1 text-caption-2 font-bold leading-[17px] text-white tabular-nums"
           x-text="tip?.value"></p>
    </x-chart.tooltip>
</div>
