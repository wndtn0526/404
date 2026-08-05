{{-- GPRO GNB (Global Navigation Bar) — Figma "GPRO_PORTFOLIO" (1002:520005 · 1002:526541)
     화면 최상단 고정 헤더. 좌측 메뉴 토글 + 서비스명, 우측 검색·알림·프로필.

     props:
       title      : 서비스명 (기본 '전자결재')
       user       : 프로필에 쓸 이름
       avatar     : 프로필 이미지 경로 (없으면 이니셜)
       hasAlarm   : true 면 알림 아이콘에 빨간 점
       searchable : 검색 버튼 노출 (기본 true)
     이벤트: 메뉴 버튼은 window 에 'lnb-toggle' 을 쏜다. x-lnb 가 받는다.

     GPRO 실측: 높이 56px · 흰 배경 · 아이콘 24px · 알림 점은 status negative. --}}
@props([
    'title' => '전자결재',
    'user' => null,
    'avatar' => null,
    'hasAlarm' => false,
    'searchable' => true,
])

@php
    $iconBtn = 'inline-flex h-9 w-9 items-center justify-center rounded-md text-label-neutral transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<header {{ $attributes->class('sticky top-0 z-50 flex h-14 w-full items-center justify-between gap-3 border-b border-line-solid-normal bg-background-normal px-4') }}>
    {{-- 좌측: 메뉴 토글 + 서비스명 --}}
    <div class="flex min-w-0 items-center gap-2">
        <button type="button" class="{{ $iconBtn }}" aria-label="메뉴 열기"
                @click="$dispatch('lnb-toggle')">
            <x-icon-menu class="h-6 w-6" />
        </button>

        <a href="/" class="truncate text-headline-2 font-bold text-label-strong">{{ $title }}</a>
    </div>

    {{-- 우측: 검색 · 알림 · 프로필 --}}
    <div class="flex items-center gap-1">
        @if ($searchable)
            <button type="button" class="{{ $iconBtn }}" aria-label="검색">
                <x-icon-search class="h-6 w-6" />
            </button>
        @endif

        <button type="button" class="{{ $iconBtn }} relative" aria-label="알림{{ $hasAlarm ? ' (새 알림 있음)' : '' }}">
            <x-icon-bell class="h-6 w-6" />
            @if ($hasAlarm)
                {{-- notification_dot_12 --}}
                <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-status-negative ring-2 ring-background-normal"></span>
            @endif
        </button>

        {{ $actions ?? '' }}

        <x-thumbnail :src="$avatar" :name="$user" size="sm" class="ml-1" />
    </div>
</header>
