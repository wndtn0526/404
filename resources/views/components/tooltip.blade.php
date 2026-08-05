{{-- 원본 Tooltip — Figma 디자인 가이드 (1002:522381 · Tooltip Text Only)
     트리거에 마우스를 올리거나 포커스하면 뜨는 짧은 설명. 긴 문장은 넣지 않는다.

     사용:
       <x-tooltip text="반려하면 기안자에게 돌아갑니다">
           <x-button variant="danger">반려</x-button>
       </x-tooltip>

     props:
       text     : 표시할 문구 (필수)
       position : top(기본) | bottom | left | right
     원본 실측: 어두운 면에 밝은 11px 텍스트 · 패딩 10px · 반경 6px · 꼬리 14×8px. --}}
@props([
    'text' => '',
    'position' => 'top',
])

@php
    // 완성된 클래스명을 담는다 (런타임 조립 금지 — Tailwind 가 못 찾는다)
    $panels = [
        'top' => 'bottom-full left-1/2 mb-2 -translate-x-1/2',
        'bottom' => 'top-full left-1/2 mt-2 -translate-x-1/2',
        'left' => 'right-full top-1/2 mr-2 -translate-y-1/2',
        'right' => 'left-full top-1/2 ml-2 -translate-y-1/2',
    ];
    $tails = [
        'top' => 'left-1/2 top-full -translate-x-1/2 -translate-y-1/2',
        'bottom' => 'left-1/2 bottom-full -translate-x-1/2 translate-y-1/2',
        'left' => 'left-full top-1/2 -translate-x-1/2 -translate-y-1/2',
        'right' => 'right-full top-1/2 translate-x-1/2 -translate-y-1/2',
    ];
    $panel = $panels[$position] ?? $panels['top'];
    $tail = $tails[$position] ?? $tails['top'];
    $id = 'tip-' . uniqid();
@endphp

<span {{ $attributes->class('relative inline-flex') }}
      x-data="{ open: false }"
      @mouseenter="open = true" @mouseleave="open = false"
      @focusin="open = true" @focusout="open = false">
    <span aria-describedby="{{ $id }}" class="inline-flex">{{ $slot }}</span>

    <span x-show="open" x-cloak id="{{ $id }}" role="tooltip"
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
          class="pointer-events-none absolute z-[80] {{ $panel }} whitespace-nowrap rounded-lg bg-inverse-background px-2.5 py-1.5 text-caption-2 text-inverse-label shadow-elevation-sm">
        {{ $text }}
        {{-- 꼬리 — 패널과 같은 배경을 45° 회전시켜 붙인다 --}}
        <span class="absolute {{ $tail }} h-2 w-2 rotate-45 bg-inverse-background" aria-hidden="true"></span>
    </span>
</span>
