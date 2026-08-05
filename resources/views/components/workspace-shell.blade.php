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
       rail       : 워크스페이스 레일 심볼
                    [['icon' => 'compass', 'href' => '/community', 'active' => true, 'label' => '커뮤니티'], ...]
                    icon 을 주면 DS 아이콘(symbol), mark 를 주면 브랜드 마크(투톤 SVG).
                    active 는 현재 위치. 상태별 표현은 아래 $railStyles 에 있다.
       user       : 프로필 이니셜에 쓸 이름
       hasAlarm   : 알림 아이콘에 빨간 점
       scale      : LNB 확대 배율. 1 = Figma 실측 그대로(240px).
                    1920 프레임을 축소해 보는 Figma 와 달리 실화면에서는 240px 이 고정이라
                    넓은 모니터에서 비율이 작아 보인다. CSS zoom 이라 글자도 같이 커지고
                    비율은 원본 그대로 유지된다(transform: scale 과 달리 흐려지지 않는다).

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
    'scale' => 1,
])

@php
    // 배율은 숫자로만 받는다 — style 속성에 그대로 들어가므로 문자열을 통과시키지 않는다.
    $zoom = max(0.5, min(2.0, (float) $scale));

    /*
     * href 와 활성 상태를 정규화한다.
     *  · '/' 로 시작하는 경로는 url() 로 절대 URL 로 만든다 — 정적 배포 스크립트가
     *    절대 URL 만 상대 경로로 치환하기 때문에, 루트 상대 경로로 두면 Pages 에서 깨진다.
     *  · active 를 명시하지 않았으면 현재 경로로 판정한다. match 가 있으면 그 패턴들을,
     *    없으면 href 경로 하나만 본다.
     */
    $resolveNav = function (array $entry): array {
        $href = $entry['href'] ?? '#';
        $path = str_starts_with($href, '/') ? trim($href, '/') : null;

        if (! array_key_exists('active', $entry)) {
            $patterns = (array) ($entry['match'] ?? array_filter([$path]));
            $entry['active'] = $patterns !== [] && request()->is(...$patterns);
        }

        $entry['href'] = $path !== null ? url($href) : $href;

        return $entry;
    };

    $items = array_map($resolveNav, array_values(array_filter((array) $items)));
    $footerItems = array_map($resolveNav, array_values(array_filter((array) $footerItems)));
    $rail = array_map($resolveNav, array_values(array_filter((array) $rail)));

    // 원본 실측: pl 10 · py 6 · gap 12 · 반경 6. 활성만 BG 02 를 깐다.
    $itemBase = 'flex w-[161px] items-start gap-3 rounded-lg py-1.5 pl-2.5 transition-colors';

    // 활성 = Label/Assistive 28% 면 + Background/Normal/Alternative 글자 (node 1-4530).
    // 비활성 글자는 Cool Neutral/50 — 대비 약 4:1 로 읽힌다.
    // ⚠️ 하단 '설정'만 아직 Cool Neutral/25(대비 약 1.9:1) 다. 원본(1:4013)이 갱신에서
    //    빠져 그대로 남았다. 메뉴와 같이 올릴 거면 $itemOffFooter 를 $itemOff 로 합친다.
    $itemOff = 'text-workspace-cool-50 hover:bg-workspace-cool-25/28 hover:text-workspace-white';
    $itemOffFooter = 'text-workspace-cool-25 hover:bg-workspace-cool-25/28 hover:text-workspace-white';
    $itemOn = 'bg-workspace-cool-25/28 text-workspace-white';

    // 레일 심볼 활성/비활성 — Figma node 1-4661 이 정의한 그대로.
    // 두 타일이 서로 다른 방식으로 꺼진다:
    //   symbol(나침반)  면 색이 바뀐다.  활성 Background/Normal/Alternative · 비활성 Neutral/40
    //   mark(회사 심볼) 면 색은 그대로,  비활성에서 불투명도만 30% 로 내린다.
    //                   브랜드색이라 다른 색으로 갈아치우지 않는 것이다.
    // ⚠️ Tailwind 는 파일을 문자열로 훑으므로 완성된 클래스명을 담는다.
    $railStyles = [
        'symbol' => [
            'on' => 'bg-workspace-white',
            'off' => 'bg-workspace-neutral-40 hover:bg-workspace-white/70',
        ],
        'mark' => [
            'on' => 'bg-workspace-tile-teal',
            'off' => 'bg-workspace-tile-teal opacity-30 hover:opacity-60',
        ],
    ];

    $gnbIcon = 'inline-flex size-6 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<div {{ $attributes->class('flex min-h-screen bg-mono-global-bg') }}>
    {{-- ═══ LNB — 240px 다크 사이드바 ═══ --}}
    <aside class="relative flex w-60 shrink-0 flex-col bg-workspace-bg"
           @if ($zoom !== 1.0) style="zoom: {{ $zoom }}" @endif
           x-data="{ open: false }"
           @lnb-toggle.window="open = ! open"
           :class="open ? 'max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-40' : 'max-lg:hidden'">

        {{-- 레일 구분선 — 원본 x54 · 흰색 5% --}}
        <div class="absolute inset-y-0 left-[54px] w-px bg-white/5" aria-hidden="true"></div>

        {{-- ── 워크스페이스 레일 (좌측 54px) ── --}}
        <div class="absolute left-3 top-5 flex w-[30px] flex-col gap-5">
            @foreach ($rail as $tile)
                @php
                    // mark 를 주면 브랜드 마크(투톤 SVG), icon 을 주면 DS 아이콘.
                    $mark = $tile['mark'] ?? null;
                    $railIcon = $tile['icon'] ?? null;
                    $kind = $mark ? 'mark' : 'symbol';
                    $railActive = (bool) ($tile['active'] ?? false);
                    $railStyle = $railStyles[$kind][$railActive ? 'on' : 'off'];
                    $railLabel = $tile['label'] ?? '';
                @endphp

                <a href="{{ $tile['href'] ?? '#' }}"
                   @if ($railActive) aria-current="page" @endif
                   class="flex size-[30px] items-center justify-center rounded-md transition-all {{ $railStyle }} focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                   @if ($railLabel) aria-label="{{ $railLabel }}{{ $railActive ? ' (현재 위치)' : '' }}" @endif>
                    @if ($mark)
                        {{-- 원본 실측 16 × 13.333 — 비율이 정사각이 아니라 두 값을 못 박는다 --}}
                        <x-dynamic-component :component="'brand-' . $mark" class="h-[13.333px] w-4" />
                    @elseif ($railIcon)
                        {{-- 글리프는 활성·비활성 모두 검정이다. 원본이 같은 에셋을 쓴다. --}}
                        <x-dynamic-component :component="'icon-' . $railIcon" class="size-[18px] text-mono-black" />
                    @endif
                </a>
            @endforeach
        </div>

        {{-- 원본(1:4005)에는 레일 하단에 워크스페이스 추가(+) 버튼이 있다. 빼기로 해서 지웠다.
             되살릴 땐 bottom 20 · left 12 · 30px · 반경 6 · BG Cool Neutral/30 · 아이콘 12px. --}}

        {{-- ── 메뉴 영역 (좌측 67px 부터) ── --}}
        <div class="flex min-h-0 flex-1 flex-col pl-[67px] pr-3">
            {{-- 워크스페이스 이름 · 도메인 — 원본 top 16 / 39 --}}
            @if ($workspace || $domain)
                <div class="pt-4">
                    @if ($workspace)
                        <p class="truncate text-body-2 font-bold leading-[21px] text-white">{{ $workspace }}</p>
                    @endif
                    @if ($domain)
                        <p class="truncate pt-[2px] text-caption-2 leading-[14px] text-workspace-cool-50">{{ $domain }}</p>
                    @endif
                </div>
            @endif

            {{-- 주요 메뉴 — 원본이 auto-layout 컨테이너(1:4530)로 바뀌었다. top 70 · gap 16.
                 이름/도메인 블록이 53 까지 차지하므로 pt 17. --}}
            <nav class="flex flex-col gap-4 pt-[17px]" aria-label="주요 메뉴">
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
                        <span class="truncate pt-[2px] text-label-1 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- 하단 메뉴 — 원본 bottom 17 --}}
            @if ($footerItems)
                <nav class="mt-auto flex flex-col gap-4 pb-[17px]" aria-label="보조 메뉴">
                    @foreach ($footerItems as $item)
                        @php
                            $active = (bool) ($item['active'] ?? false);
                        @endphp

                        <a href="{{ $item['href'] ?? '#' }}"
                           @if ($active) aria-current="page" @endif
                           @class([$itemBase, $itemOn => $active, $itemOffFooter => ! $active])>
                            @if (! empty($item['icon']))
                                <x-dynamic-component :component="'icon-' . $item['icon']" class="size-6 shrink-0" />
                            @endif
                            <span class="truncate pt-[2px] text-label-1 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
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
