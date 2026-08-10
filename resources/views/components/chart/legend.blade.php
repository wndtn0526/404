{{-- 차트 범례 — Figma GPRO_PORTFOLIO node 1002-88919 실측:
     표식 10x10 반경 2 · 표식↔글자 6 · 항목 사이 18 · 글자 12 (lh 18) 검정.
     색은 완성된 유틸리티 클래스로 받는다.

     props
       items : [['label' =>, 'class' => 'text-purple-900'], …] --}}
@props(['items' => []])

<ul {{ $attributes->class('flex flex-wrap items-center gap-x-[18px] gap-y-1.5') }}>
    @foreach ($items as $item)
        <li class="flex items-center gap-1.5 {{ $item['class'] }}">
            <span class="size-2.5 shrink-0 rounded-xs bg-current" aria-hidden="true"></span>
            <span class="text-caption-1 leading-[18px] text-mono-black">{{ $item['label'] }}</span>
        </li>
    @endforeach
</ul>
