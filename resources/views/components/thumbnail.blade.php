{{-- 원본 Thumbnail / Profile — Figma 디자인 가이드 (1002:523077 · 1002:522932)
     이미지가 있으면 이미지를, 없으면 이름 첫 글자를 색 배경 위에 보여준다.

     props:
       src      : 이미지 경로 (없으면 이니셜 폴백)
       name     : 이니셜·alt 에 쓰는 이름
       size     : xs(24) | sm(32) | md(40) | lg(48) | xl(64) | 2xl(120)
       shape    : circle(프로필 · 기본) | square(썸네일 · 4px)
       fallback : auto(기본 · 이름 있으면 이니셜, 없으면 사람 아이콘) | none(면만)
                  none 은 위에 다른 걸 얹는 자리다 — 사진 올리기 버튼처럼.
                  이니셜(흰 글자)과 얹은 글리프가 겹쳐서 둘 다 안 읽히는 것을 막는다.
     원본 실측: Profile 은 원형, Thumbnail 은 4px 사각. 폴백 배경은 deep blue 800. --}}
@props([
    'src' => null,
    'name' => null,
    'size' => 'md',
    'shape' => 'circle',
    'fallback' => 'auto',
    /*
     * nameExpr: 이름이 Alpine 쪽에만 있을 때 쓰는 식 (예: "m.name").
     * 서버는 이름을 모르므로 이니셜 자리만 만들어 두고 글자는 Alpine 이 채운다.
     * x-for 로 찍는 목록에서 아바타를 손으로 만들지 않게 하려고 둔 것이다.
     */
    'nameExpr' => null,
])

@php
    // 원본이니셜 글자 크기 실측: 11 / 12 / 15 / 18 / 24px → DS 타이포 단계로 매핑
    $sizes = [
        'xs' => ['box' => 'h-6 w-6', 'text' => 'text-caption-2'],
        'sm' => ['box' => 'h-8 w-8', 'text' => 'text-caption-1'],
        'md' => ['box' => 'h-10 w-10', 'text' => 'text-body-2'],
        'lg' => ['box' => 'h-12 w-12', 'text' => 'text-headline-2'],
        'xl' => ['box' => 'h-16 w-16', 'text' => 'text-title-3'],
        // 프로필 화면용 큰 단계. 원본 실측 120px (GPRO_PORTFOLIO node 1104-58542).
        '2xl' => ['box' => 'h-30 w-30', 'text' => 'text-display-3'],
    ];
    $sz = $sizes[$size] ?? $sizes['md'];

    // Tailwind 는 클래스명을 문자열로 훑으므로 완성된 클래스명을 담는다
    $round = $shape === 'square' ? 'rounded-md' : 'rounded-full';

    $initial = filled($name) ? mb_substr(trim($name), 0, 1) : null;
@endphp

{{-- 폴백 배경은 $attributes->class 안에서 조건부로 합친다. @class 를 따로 쓰면
     같은 태그에 class 속성이 두 번 나가고 뒤쪽이 무시돼 배경이 사라진다. --}}
<span {{ $attributes->class([
    "inline-flex shrink-0 items-center justify-center overflow-hidden {$sz['box']} {$round}",
    'bg-deep-blue-800' => blank($src),
]) }}>
    @if (filled($src))
        <img src="{{ $src }}" alt="{{ $name ?? '' }}" class="h-full w-full object-cover" />
    @elseif ($fallback === 'none')
        {{-- 면만 그린다. 부르는 쪽이 위에 무언가를 얹는다. --}}
    @elseif ($nameExpr)
        <span class="{{ $sz['text'] }} font-semibold text-white select-none"
              x-text="String({{ $nameExpr }} ?? '').trim().slice(0, 1)"></span>
    @elseif ($initial)
        <span class="{{ $sz['text'] }} font-semibold text-white select-none">{{ $initial }}</span>
    @else
        <x-icon-person class="h-1/2 w-1/2 text-white" />
    @endif
</span>
