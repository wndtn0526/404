{{-- 원본 Thumbnail / Profile — Figma 디자인 가이드 (1002:523077 · 1002:522932)
     이미지가 있으면 이미지를, 없으면 이름 첫 글자를 색 배경 위에 보여준다.

     props:
       src    : 이미지 경로 (없으면 이니셜 폴백)
       name   : 이니셜·alt 에 쓰는 이름
       size   : xs(24) | sm(32) | md(40) | lg(48) | xl(64)
       shape  : circle(프로필 · 기본) | square(썸네일 · 4px)
     원본 실측: Profile 은 원형, Thumbnail 은 4px 사각. 폴백 배경은 deep blue 800. --}}
@props([
    'src' => null,
    'name' => null,
    'size' => 'md',
    'shape' => 'circle',
])

@php
    // 원본이니셜 글자 크기 실측: 11 / 12 / 15 / 18 / 24px → DS 타이포 단계로 매핑
    $sizes = [
        'xs' => ['box' => 'h-6 w-6', 'text' => 'text-caption-2'],
        'sm' => ['box' => 'h-8 w-8', 'text' => 'text-caption-1'],
        'md' => ['box' => 'h-10 w-10', 'text' => 'text-body-2'],
        'lg' => ['box' => 'h-12 w-12', 'text' => 'text-headline-2'],
        'xl' => ['box' => 'h-16 w-16', 'text' => 'text-title-3'],
    ];
    $sz = $sizes[$size] ?? $sizes['md'];

    // Tailwind 는 클래스명을 문자열로 훑으므로 완성된 클래스명을 담는다
    $round = $shape === 'square' ? 'rounded-md' : 'rounded-full';

    $initial = filled($name) ? mb_substr(trim($name), 0, 1) : null;
@endphp

<span {{ $attributes->class("inline-flex shrink-0 items-center justify-center overflow-hidden {$sz['box']} {$round}") }}
      @class(['bg-deep-blue-800' => blank($src)])>
    @if (filled($src))
        <img src="{{ $src }}" alt="{{ $name ?? '' }}" class="h-full w-full object-cover" />
    @elseif ($initial)
        <span class="{{ $sz['text'] }} font-semibold text-white select-none">{{ $initial }}</span>
    @else
        <x-icon-person class="h-1/2 w-1/2 text-white" />
    @endif
</span>
