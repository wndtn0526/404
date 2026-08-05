{{-- 원본 LNB (Local Navigation Bar) — Figma 디자인 가이드 (1002:525753 · 1002:526580)
     좌측 사이드 내비게이션. 어두운 면 위에 메뉴 항목을 세로로 쌓는다.

     props:
       items   : [['label' => '결재함', 'href' => '/approvals', 'icon' => 'inbox',
                   'active' => true, 'badge' => 3], ...]
                 icon 은 DS 아이콘 이름(<x-icon-{이름} />). badge 는 숫자 배지(선택).
       heading : 상단 영역 제목 (선택)
     이벤트: GNB 의 메뉴 버튼이 쏘는 'lnb-toggle' 로 모바일에서 열고 닫는다.

     원본 실측: 너비 240px · 헤더 72px · 항목 높이 32px / 14px 텍스트 · 반경 3px
                배경 Side Bar BG 01, 접힌 영역 BG 02 · 비활성 텍스트 warm gray 500. --}}
@props([
    'items' => [],
    'heading' => null,
])

@php
    $items = array_values(array_filter((array) $items));
@endphp

<aside {{ $attributes->class('flex w-60 shrink-0 flex-col bg-sidebar-bg-01') }}
       x-data="{ open: false }"
       @lnb-toggle.window="open = ! open"
       :class="open ? 'max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-40' : 'max-lg:hidden'">

    @if ($heading)
        {{-- 헤더 72px --}}
        <div class="flex h-18 items-center px-4">
            <span class="truncate text-body-1 font-bold text-inverse-label">{{ $heading }}</span>
        </div>
    @endif

    <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto p-2" aria-label="주요 메뉴">
        @foreach ($items as $item)
            @php
                $label = $item['label'] ?? '';
                $href = $item['href'] ?? '#';
                $icon = $item['icon'] ?? null;
                $active = (bool) ($item['active'] ?? false);
                $badge = $item['badge'] ?? null;
            @endphp

            <a href="{{ $href }}"
               @if ($active) aria-current="page" @endif
               @class([
                   'flex h-8 items-center gap-2.5 rounded-sm px-3 text-label-1 transition-colors',
                   'bg-sidebar-bg-02 font-semibold text-inverse-label' => $active,
                   'text-interaction-inactive hover:bg-sidebar-bg-02 hover:text-label-assistive' => ! $active,
               ])>
                @if ($icon)
                    <x-dynamic-component :component="'icon-' . $icon" class="h-5 w-5 shrink-0" />
                @endif

                <span class="flex-1 truncate">{{ $label }}</span>

                @if (filled($badge))
                    <span class="inline-flex h-4 min-w-4 items-center justify-center rounded-xs bg-status-negative px-1 text-caption-2 font-semibold text-white">
                        {{ $badge }}
                    </span>
                @endif
            </a>
        @endforeach
    </nav>

    {{ $footer ?? '' }}
</aside>
