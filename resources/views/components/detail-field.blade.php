{{-- 상세 정보 한 칸 — 라벨 + 값을 나란히 둔다.
     Figma GPRO_PORTFOLIO node 1002-275959 (인사 상세정보) 실측:
       라벨 94 · 라벨↔값 16 · 줄 20 · 행 사이 16 (피치 36)
       라벨 14 Medium Warm gray/500  → DS label-1 과 정확히 일치
       값   14 Medium 검정            → DS label-1 과 정확히 일치

     <dl> 안에서 쓴다 — 라벨은 <dt>, 값은 <dd> 로 나간다.

     props:
       label : 라벨
       value : 값. 비면 하이픈으로 나간다. 배지처럼 마크업이 필요하면 슬롯을 쓴다. --}}
@props([
    'label' => null,
    'value' => null,
])

<div {{ $attributes->class('flex min-w-0 items-start gap-4') }}>
    <dt class="w-[94px] shrink-0 text-label-1 font-medium leading-5 text-warm-gray-500">{{ $label }}</dt>
    <dd class="min-w-0 flex-1 text-label-1 font-medium leading-5 text-mono-black">
        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            {{ filled($value) ? $value : '-' }}
        @endif
    </dd>
</div>
