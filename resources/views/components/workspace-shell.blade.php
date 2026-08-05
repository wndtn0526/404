{{-- 워크스페이스 셸 — Figma 워크스페이스 화면 (Lnsej46BaxtyKq3rhssFH3 · node 1-299)
     어두운 LNB(레일 + 메뉴) + 투명 GNB + 본문 영역. 이 앱의 모든 화면이 공유하는 크롬이다.

     ⚠️ DS 의 <x-gnb> · <x-lnb> 와는 구조가 다르다. DS 쪽은 흰 헤더 + 평평한 240px 리스트이고,
        이쪽은 워크스페이스 레일(54px)이 붙은 다크 LNB + 배경 투명 헤더다. 원본이 그래서
        따로 만들었다. 둘을 섞지 말고 화면 단위로 하나만 고른다.

     props:
       workspace  : 워크스페이스 이름 (LNB 상단 굵은 글씨)
       domain     : 워크스페이스 도메인 (이름 아래 작은 글씨)
       items      : LNB 메뉴 [['label' => '홈', 'href' => '/', 'icon' => 'home', 'active' => true], ...]
       footerItems: LNB 하단에 붙는 메뉴(설정 등). items 와 같은 모양.
       rail       : 워크스페이스 레일 타일 [['icon' => 'compass', 'tone' => 'neutral', 'label' => '…'], ...]
                    tone 은 neutral | teal.
       user       : 프로필 이니셜에 쓸 이름
       hasAlarm   : 알림 아이콘에 빨간 점

     슬롯:
       breadcrumb / title / actions / (기본 슬롯 = 본문)

     원본 실측 — LNB 240 · 레일 30px 타일(top 20, gap 20) · 구분선 x54 흰색 5%
                 메뉴 항목 left 67 · w 161 · pl 10 · py 6 · 반경 6 · 아이콘·텍스트 간격 12
                 GNB 56 · 아이콘 24 · 프로필 32 · 본문 좌측 320 · 우측 여백 80 --}}
@props([
    'workspace' => null,
    'domain' => null,
    'items' => [],
    'footerItems' => [],
    'rail' => [],
    'user' => null,
    'hasAlarm' => false,
])

@php
    $items = array_values(array_filter((array) $items));
    $footerItems = array_values(array_filter((array) $footerItems));
    $rail = array_values(array_filter((array) $rail));

    // 원본 실측: pl 10 · py 6 · gap 12 · 반경 6. 활성만 BG 02 를 깐다.
    $itemBase = 'flex w-[161px] items-start gap-3 rounded-lg py-1.5 pl-2.5 transition-colors';

    // ⚠️ 비활성 텍스트는 원본이 label/alternative(rgba(55,56,60,0.61)) 를 쓴다. 그 값을
    //    Side Bar/BG 01 위에 얹으면 대비가 1.2:1 로 떨어져 사실상 안 보인다. 같은 성격의
    //    '설정' 항목은 원본에서 Warm gray/700 이라 그쪽에 맞췄다. 원본을 글자 그대로
    //    따르면 읽을 수 없는 라벨이 나온다.
    $itemOff = 'text-warm-gray-700 hover:bg-sidebar-bg-02 hover:text-warm-gray-500';
    $itemOn = 'bg-sidebar-bg-02 text-white';

    // 레일 타일 색 — Tailwind 는 파일을 문자열로 훑으므로 완성된 클래스명을 담는다.
    $railTones = [
        'neutral' => 'bg-workspace-tile-neutral',
        'teal' => 'bg-workspace-tile-teal',
    ];

    $gnbIcon = 'inline-flex size-6 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<div {{ $attributes->class('flex min-h-screen bg-mono-global-bg') }}>
    {{-- ═══ LNB — 240px 다크 사이드바 ═══ --}}
    <aside class="relative flex w-60 shrink-0 flex-col bg-sidebar-bg-01"
           x-data="{ open: false }"
           @lnb-toggle.window="open = ! open"
           :class="open ? 'max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-40' : 'max-lg:hidden'">

        {{-- 레일 구분선 — 원본 x54 · 흰색 5% --}}
        <div class="absolute inset-y-0 left-[54px] w-px bg-white/5" aria-hidden="true"></div>

        {{-- ── 워크스페이스 레일 (좌측 54px) ── --}}
        <div class="absolute left-3 top-5 flex w-[30px] flex-col gap-5">
            @foreach ($rail as $tile)
                @php
                    $tone = $railTones[$tile['tone'] ?? 'neutral'] ?? $railTones['neutral'];
                    // mark 를 주면 브랜드 마크(투톤 SVG), icon 을 주면 DS 아이콘.
                    $mark = $tile['mark'] ?? null;
                    $railIcon = $tile['icon'] ?? null;
                @endphp

                <button type="button"
                        class="flex size-[30px] items-center justify-center rounded-md {{ $tone }} transition-opacity hover:opacity-80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                        @if (! empty($tile['label'])) aria-label="{{ $tile['label'] }}" @endif>
                    @if ($mark)
                        {{-- 원본 실측 16 × 13.333 — 비율이 정사각이 아니라 두 값을 못 박는다 --}}
                        <x-dynamic-component :component="'brand-' . $mark" class="h-[13.333px] w-4" />
                    @elseif ($railIcon)
                        <x-dynamic-component :component="'icon-' . $railIcon" class="size-[18px] text-white" />
                    @endif
                </button>
            @endforeach
        </div>

        {{-- 워크스페이스 추가 — 원본 bottom 20 · BG Mono/900 --}}
        <button type="button"
                class="absolute bottom-5 left-3 flex size-[30px] items-center justify-center rounded-lg bg-mono-900 text-white transition-colors hover:bg-warm-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                aria-label="워크스페이스 추가">
            <x-icon-plus class="size-3" />
        </button>

        {{-- ── 메뉴 영역 (좌측 67px 부터) ── --}}
        <div class="flex min-h-0 flex-1 flex-col pl-[67px] pr-3">
            {{-- 워크스페이스 이름 · 도메인 — 원본 top 16 / 39 --}}
            @if ($workspace || $domain)
                <div class="pt-4">
                    @if ($workspace)
                        <p class="truncate text-body-2 font-bold leading-[21px] text-white">{{ $workspace }}</p>
                    @endif
                    @if ($domain)
                        <p class="truncate pt-[2px] text-caption-2 leading-[14px] text-warm-gray-700">{{ $domain }}</p>
                    @endif
                </div>
            @endif

            {{-- 주요 메뉴 — 원본 첫 항목 top 70, 두 번째 top 121.
                 항목 높이 36(아이콘 24 + py 6·6) → 간격 15. 이름/도메인 블록이 53 까지 차지하므로 pt 17. --}}
            <nav class="flex flex-col gap-[15px] pt-[17px]" aria-label="주요 메뉴">
                @foreach ($items as $item)
                    @php
                        $active = (bool) ($item['active'] ?? false);
                    @endphp

                    <a href="{{ $item['href'] ?? '#' }}"
                       @if ($active) aria-current="page" @endif
                       @class([$itemBase, $itemOn => $active, $itemOff => ! $active])>
                        @if (! empty($item['icon']))
                            <x-dynamic-component :component="'icon-' . $item['icon']" class="size-6 shrink-0" />
                        @endif
                        <span class="truncate pt-[2px] text-label-2 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- 하단 메뉴 — 원본 bottom 17 --}}
            @if ($footerItems)
                <nav class="mt-auto flex flex-col gap-[15px] pb-[17px]" aria-label="보조 메뉴">
                    @foreach ($footerItems as $item)
                        @php
                            $active = (bool) ($item['active'] ?? false);
                        @endphp

                        <a href="{{ $item['href'] ?? '#' }}"
                           @if ($active) aria-current="page" @endif
                           @class([$itemBase, $itemOn => $active, $itemOff => ! $active])>
                            @if (! empty($item['icon']))
                                <x-dynamic-component :component="'icon-' . $item['icon']" class="size-6 shrink-0" />
                            @endif
                            <span class="truncate pt-[2px] text-label-2 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </aside>

    {{-- ═══ 본문 영역 ═══ --}}
    <div class="flex min-w-0 flex-1 flex-col">
        {{-- ── GNB 56px — 원본은 배경이 투명하다(navigation_BG opacity 0) ── --}}
        <header class="flex h-14 shrink-0 items-center justify-between px-6">
            {{-- 좌측: 사이드바 접기 — 원본 24x24 · BG Warm gray/200 · 반경 4 --}}
            <button type="button"
                    class="flex size-6 items-center justify-center rounded-md bg-warm-gray-200 text-label-normal transition-colors hover:bg-warm-gray-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                    aria-label="사이드바 접기"
                    @click="$dispatch('lnb-toggle')">
                <x-icon-chevron-left class="size-4" />
            </button>

            {{-- 우측: 검색 · 알림 · 앱 · 프로필 — 원본 우측에서 176 / 128 / 80 / 24 --}}
            <div class="flex items-center gap-8">
                <button type="button" class="{{ $gnbIcon }}" aria-label="검색">
                    <x-icon-search class="size-6" />
                </button>

                <button type="button" class="{{ $gnbIcon }} relative" aria-label="알림">
                    <x-icon-bell class="size-6" />
                    @if ($hasAlarm)
                        <span class="absolute right-0 top-0 size-[6px] rounded-full bg-status-negative" aria-hidden="true"></span>
                        <span class="sr-only">읽지 않은 알림 있음</span>
                    @endif
                </button>

                <button type="button" class="{{ $gnbIcon }}" aria-label="앱 전환">
                    <x-icon-apps class="size-6" />
                </button>

                {{-- 프로필 — 원본은 스톡 인물 사진이다. 공개 저장소에 사진을 넣지 않고
                     DS 썸네일(이니셜)로 대신했다. 실제 사용자 이미지가 붙으면 교체한다. --}}
                <x-thumbnail :name="$user" size="sm" shape="circle" class="shrink-0" />
            </div>
        </header>

        {{-- ── 페이지 헤더 — 원본 좌측 320(LNB 240 + 80) · 우측 80 ── --}}
        <div class="px-20">
            @if (isset($breadcrumb))
                <div>{{ $breadcrumb }}</div>
            @endif

            {{-- 원본: 브레드크럼 top 56(높이 20) · 타이틀 top 106 → 사이 30 --}}
            <div class="flex flex-wrap items-center justify-between gap-6 pt-[30px]">
                @if (isset($title))
                    <div class="flex flex-wrap items-center gap-8">{{ $title }}</div>
                @endif

                @if (isset($actions))
                    <div class="flex items-center gap-2">{{ $actions }}</div>
                @endif
            </div>
        </div>

        {{-- ── 본문 ── --}}
        <main class="min-h-0 flex-1 px-20 pb-20 pt-8">
            {{ $slot }}
        </main>
    </div>
</div>
