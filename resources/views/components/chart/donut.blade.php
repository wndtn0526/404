{{-- 도넛 차트 — Figma GPRO_PORTFOLIO node 1002-88964 실측: 바깥 지름 160.
     조각 색은 완성된 유틸리티 클래스로 받는다.

     ⚠️ 원본은 도넛이 통 SVG 한 장으로 내려온다. 그 파일을 그대로 쓰면 값이 바뀌어도
        그림이 안 바뀐다 — 차트는 데이터에서 나와야 해서 각도로 다시 그렸다.
        아이콘·삽화였다면 내려받은 파일을 그대로 썼을 것이다.
     ⚠️ 안쪽 구멍 지름은 원본 이미지에서 눈으로 잰 값이다(테두리 34).

     props
       slices : [['label' =>, 'value' =>, 'class' => 'text-purple-900'], …] --}}
@props(['slices' => []])

@php
    $total = collect($slices)->sum('value') ?: 1;
    $r = 63;            // 테두리 한가운데 반지름 (바깥 80 · 안쪽 46)
    $circumference = 2 * M_PI * $r;
    $offset = 0.0;      // 12시부터 시계방향
@endphp

<svg {{ $attributes->class('block size-40') }} viewBox="0 0 160 160" role="img" aria-label="비용 비율">
    @foreach ($slices as $slice)
        @php
            $len = $circumference * ($slice['value'] / $total);
        @endphp
        <circle cx="80" cy="80" r="{{ $r }}" fill="none" stroke="currentColor" stroke-width="34"
                stroke-dasharray="{{ round($len, 2) }} {{ round($circumference - $len, 2) }}"
                stroke-dashoffset="{{ round(-$offset, 2) }}"
                transform="rotate(-90 80 80)"
                class="{{ $slice['class'] }}" />
        @php $offset += $len; @endphp
    @endforeach
</svg>
