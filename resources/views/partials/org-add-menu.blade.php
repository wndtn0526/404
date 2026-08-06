{{-- 조직 추가 버튼(+)과 눌렀을 때 열리는 메뉴 — Figma GPRO_PORTFOLIO node 1002-279241.

     원본 실측 — 버튼 24 · 반경 6 · 면 Mono/800
       메뉴 282x114 · 반경 6 · 안쪽 좌우 5 · 위아래 8 · 버튼 오른쪽 아래 꼭짓점에서 열린다
       항목 272x49 · 안쪽 왼쪽 15 · 아이콘 타일 24 (면 Warm gray/100 · 반경 6) · 간격 8
       글자 15 Bold lh23 -0.6 → DS body-2 와 정확히 일치
       항목은 '법인 추가' · '조직 추가' 두 개

     ⚠️ 원본 항목 글자색이 Warm gray/400 이다. 그대로 두면 메뉴가 꺼져 있는 것처럼 읽혀서
        label-normal 로 올렸다. 아이콘 타일 색은 원본 그대로다.
     ⚠️ 그림자는 원본이 0 16px 24px 계열(그리고 0 6px 30px)이다. DS 는 띄운 면에
        shadow-elevation-lg 를 쓰기로 했고(CLAUDE.md) 그 값이 0 10px 18px 라 조금 얕다.
     ⚠️ 두 항목 다 아직 동작하지 않는다. 붙일 때는 POST + CSRF 로 보낸다.

     변수:
       label = 버튼의 aria-label (기본 '조직 추가') --}}
@php
    $label = $label ?? '조직 추가';
    $items = ['법인 추가', '조직 추가'];
@endphp

<div class="relative" x-data="{ open: false }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    <button type="button" @click="open = ! open"
            class="flex size-6 items-center justify-center rounded-lg bg-mono-800 text-white transition-colors hover:bg-mono-black focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
            x-bind:aria-expanded="open"
            aria-haspopup="true"
            aria-label="{{ $label }}">
        <x-icon-plus class="size-3.5" />
    </button>

    {{-- 버튼 오른쪽 아래 꼭짓점에서 열린다(원본 그대로). Alpine 이 붙기 전에는 감춰져 있다. --}}
    <div x-show="open" x-cloak role="menu"
         class="absolute left-full top-full z-30 w-[282px] rounded-lg bg-background-normal px-[5px] py-2 shadow-elevation-lg">
        @foreach ($items as $item)
            <button type="button" role="menuitem"
                    class="flex h-[49px] w-full min-w-0 items-center gap-2 rounded-lg px-[15px] text-left transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:bg-fill-alternative">
                <span class="flex size-6 shrink-0 items-center justify-center rounded-lg bg-warm-gray-100" aria-hidden="true">
                    <x-icon-plus class="size-2.5 text-label-normal" />
                </span>
                <span class="truncate text-body-2 font-bold leading-[23px] text-label-normal">{{ $item }}</span>
            </button>
        @endforeach
    </div>
</div>
