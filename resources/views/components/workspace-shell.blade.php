{{-- 워크스페이스 셸 — Figma 워크스페이스 화면 (Lnsej46BaxtyKq3rhssFH3 · node 1-299)
     어두운 LNB(레일 + 메뉴) + 투명 GNB + 본문 영역. 이 앱의 모든 화면이 공유하는 크롬이다.

     ⚠️ DS 의 <x-gnb> · <x-lnb> 와는 구조가 다르다. DS 쪽은 흰 헤더 + 평평한 240px 리스트이고,
        이쪽은 워크스페이스 레일(54px)이 붙은 다크 LNB + 배경 투명 헤더다. 원본이 그래서
        따로 만들었다. 둘을 섞지 말고 화면 단위로 하나만 고른다.

     props:
       workspace  : 워크스페이스 이름 (LNB 상단 굵은 글씨)
       domain     : 워크스페이스 도메인 (이름 아래 작은 글씨)
       items      : LNB 메뉴 [['label' => '홈', 'href' => '/', 'icon' => 'home', 'active' => true], ...]
                    'children' => [...] 를 주면 그 아래 한 단을 더 그린다(아이콘 없이 글자만).
                    자식이 있으면 부모는 링크가 아니라 폴더가 된다 — 눌러서 접고 편다.
                    처음 상태는 '지금 이 묶음 안에 있으면 펼침'. 자식이 켜지면 부모도 켜진다.
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
                 메뉴 항목 left 67 · w 161 · pl 10 · py 6 · 반경 6 · 아이콘 20 · 간격 12
                 항목 높이 32 · 하위 항목 28 (GPRO 화면 LNB 실측)
                 ⚠️ 항목 사이만 원본 0 이 아니라 8 이다 — 0 은 실화면에서 너무 붙어 보인다
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

    /*
     * 하위 메뉴 — 원본 LNB 는 워크스페이스·인사·재무 밑에 한 단을 더 둔다
     * (전자결재 node 1002-106228 · 재무 node 1002-93118 실측).
     * 하위 항목은 아이콘이 없고 들여쓰기도 없다. 부모와 같은 자리에 글자만 온다.
     * 부모가 켜지는 조건에 자식 경로도 넣어야 한다 — 자식에 있을 때 부모가 꺼지면
     * 어느 묶음에 있는지가 안 보인다.
     */
    $resolveTree = function (array $entry) use ($resolveNav): array {
        $children = array_values(array_filter((array) ($entry['children'] ?? [])));

        if ($children) {
            $entry['children'] = array_map($resolveNav, $children);
            $entry = $resolveNav($entry);
            $entry['active'] = $entry['active']
                || collect($entry['children'])->contains(fn ($c) => $c['active'] ?? false);

            return $entry;
        }

        return $resolveNav($entry);
    };

    $items = array_map($resolveTree, array_values(array_filter((array) $items)));
    $footerItems = array_map($resolveNav, array_values(array_filter((array) $footerItems)));
    $rail = array_map($resolveNav, array_values(array_filter((array) $rail)));

    // 원본 실측: pl 10 · py 6 · gap 12 · 반경 6 → 항목 높이 32. 활성만 BG 02 를 깐다.
    // py 는 여기 두지 않는다 — 하위 항목이 28(py 4)이라 자리를 다툰다.
    $itemBase = 'flex w-[161px] items-start gap-3 rounded-lg pl-2.5 transition-colors';

    // 활성 = Label/Assistive 28% 면 + Background/Normal/Alternative 글자 (node 1-4530).
    // 비활성 글자는 Cool Neutral/50 — 다크 면에서 대비 약 4:1 로 읽힌다.
    //
    // ⚠️ 하단 '설정'은 원본(1:4013)이 Cool Neutral/25 다. 그 값은 대비가 약 1.9:1 이라
    //    글자가 거의 안 보인다. 원본이 node 1-4530 갱신에서 빠져 옛 값이 남은 것으로 보고
    //    본문 메뉴와 같은 Cool Neutral/50 으로 올렸다. 원본대로 되돌릴 거면
    //    text-workspace-cool-25 로 바꾸면 된다.
    $itemOff = 'text-workspace-cool-50 hover:bg-workspace-cool-25/28 hover:text-workspace-white';
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
    {{-- ⚠️ 모바일 숨김(max-lg:hidden)은 정적 클래스로 둔다.
         Alpine :class 안에만 두면 Alpine 이 붙기 전(또는 못 붙는 정적 배포에서)
         LNB 가 240px 을 그대로 차지해 본문이 뷰포트 밖으로 밀린다.
         열림 상태만 Alpine 이 !flex 로 뒤집는다(!important 라 hidden 을 이긴다). --}}
    <aside class="relative flex w-60 shrink-0 flex-col bg-workspace-bg max-lg:hidden"
           @if ($zoom !== 1.0) style="zoom: {{ $zoom }}" @endif
           x-data="{ open: false }"
           @lnb-toggle.window="open = ! open"
           :class="open && 'max-lg:!flex max-lg:fixed max-lg:inset-y-0 max-lg:left-0 max-lg:z-40'">

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

            {{-- 주요 메뉴 — 이름/도메인 블록이 53 까지 차지하므로 pt 17.
                 ⚠️ 항목 사이 4 는 원본과 다르다. GPRO 화면들의 LNB 는 항목 32 에 피치도 32,
                    즉 사이가 0 이다(전자결재 node 1002-106228 · 재무 node 1002-93118).
                    0 으로 두니 실화면에서 너무 붙어 보여 8 을 띄웠다. 항목 높이(32/28)는
                    원본 그대로다. 원본대로 되돌릴 거면 이 gap-2 를 빼면 된다.
                    (처음엔 워크스페이스 원본 1:4530 의 gap 16 이었는데 그건 너무 벌어졌다.) --}}
            <nav class="flex flex-col gap-2 pt-[17px]" aria-label="주요 메뉴">
                @foreach ($items as $item)
                    @php
                        $active = (bool) ($item['active'] ?? false);
                        $children = $item['children'] ?? [];
                    @endphp

                    <div class="flex flex-col gap-2"
                         @if ($children) x-data="{ open: {{ $active ? 'true' : 'false' }} }" @endif>

                        @if ($children)
                            {{-- 자식이 있으면 폴더다. 눌러서 접고 편다 — 갈 곳은 자식이 갖는다.
                                 처음 상태는 '지금 이 묶음 안에 있으면 펼침' 이다. --}}
                            <button type="button" @click="open = ! open" x-bind:aria-expanded="open"
                                    @class([$itemBase, 'py-1.5 pr-2.5 text-left', $itemOn => $active, $itemOff => ! $active])>
                                @if (! empty($item['icon']))
                                    <x-dynamic-component :component="'icon-' . $item['icon']" class="size-5 shrink-0" />
                                @endif
                                <span class="min-w-0 flex-1 truncate text-label-1 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
                                {{-- 회전은 감싼 span 에 건다. blade-icons 가 뱉는 SVG 에 직접 걸어도
                                     되지만, 아이콘 파일이 바뀌어도 회전이 딸려가지 않게 분리했다.
                                     ⚠️ Tailwind 4 의 rotate 유틸은 transform 이 아니라 rotate 속성을
                                        쓴다. 헤드리스로 확인할 땐 getComputedStyle(el).rotate 를 본다
                                        — transform 은 none 으로 나와서 안 도는 줄 착각한다. --}}
                                <span class="shrink-0 transition-transform duration-200"
                                      x-bind:class="open ? '' : '-rotate-90'">
                                    <x-icon-caret-down class="size-4" />
                                </span>
                            </button>
                        @else
                            <a href="{{ $item['href'] ?? '#' }}"
                               @if ($active) aria-current="page" @endif
                               @class([$itemBase, 'py-1.5', $itemOn => $active, $itemOff => ! $active])>
                                @if (! empty($item['icon']))
                                    <x-dynamic-component :component="'icon-' . $item['icon']" class="size-5 shrink-0" />
                                @endif
                                <span class="truncate text-label-1 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
                            </a>
                        @endif

                        {{-- 하위 메뉴 — 원본은 아이콘 없이 글자만, 들여쓰기는 없다.
                             부모가 아이콘 자리(20 + 간격 12)를 쓰므로 그만큼 왼쪽 여백을 준다.
                             높이는 28 이다(부모 32 보다 한 단 낮다 — 재무 node 1002-93118 실측).

                             ⚠️ 감싸개에 display 유틸을 static class 로 두지 않는다. hidden 과 자리를
                                다퉈서 접어도 그대로 보인다(CLAUDE.md 의 display 함정). --}}
                        @if ($children)
                            <div x-bind:class="open ? 'flex flex-col gap-2' : 'hidden'">
                                @foreach ($children as $child)
                                    @php $childActive = (bool) ($child['active'] ?? false); @endphp
                                    <a href="{{ $child['href'] ?? '#' }}"
                                       @if ($childActive) aria-current="page" @endif
                                       @class([$itemBase, 'py-1 pl-[42px]', $itemOn => $childActive, $itemOff => ! $childActive])>
                                        <span class="truncate text-label-1 font-medium leading-5">{{ $child['label'] ?? '' }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>

            {{-- 하단 메뉴 — 원본 bottom 17 --}}
            @if ($footerItems)
                <nav class="mt-auto flex flex-col gap-2 pb-[17px]" aria-label="보조 메뉴">
                    @foreach ($footerItems as $item)
                        @php
                            $active = (bool) ($item['active'] ?? false);
                        @endphp

                        <a href="{{ $item['href'] ?? '#' }}"
                           @if ($active) aria-current="page" @endif
                           @class([$itemBase, 'py-1.5', $itemOn => $active, $itemOff => ! $active])>
                            @if (! empty($item['icon']))
                                <x-dynamic-component :component="'icon-' . $item['icon']" class="size-5 shrink-0" />
                            @endif
                            <span class="truncate text-label-1 font-medium leading-5">{{ $item['label'] ?? '' }}</span>
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </aside>

    {{-- ═══ 본문 영역 ═══ --}}
    <div class="flex min-w-0 flex-1 flex-col">
        {{-- ── GNB 56px — 원본은 배경이 투명하다(navigation_BG opacity 0) ── --}}
        <header class="flex h-14 shrink-0 items-center justify-between px-5 lg:px-6">
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
        <div class="px-5 lg:px-20">
            @if (isset($breadcrumb))
                <div>{{ $breadcrumb }}</div>
            @endif

            {{-- 원본: 브레드크럼 top 56(높이 20) · 타이틀 top 106 → 사이 30
                 ⚠️ 슬롯이 둘 다 없으면 이 줄 자체를 내지 않는다. 빈 채로 두면 pt-30 만
                    남아서 본문이 30px 아래로 밀린다(퍼블릭 스페이스·게시글 상세가 그랬다). --}}
            @if (isset($title) || isset($actions))
                <div class="flex flex-wrap items-center justify-between gap-6 pt-[30px]">
                    @if (isset($title))
                        <div class="flex flex-wrap items-center gap-8">{{ $title }}</div>
                    @endif

                    @if (isset($actions))
                        <div class="flex items-center gap-2">{{ $actions }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- ── 본문 ── --}}
        {{-- min-w-0: main 은 flex 아이템이다. 없으면 min-width:auto 가 콘텐츠 기준 최소폭으로
                 잡혀 좁은 화면에서 본문이 뷰포트를 넘어간다. 좌우 여백은 원본 실측 PC 80 · 모바일 20. --}}
        <main class="min-h-0 min-w-0 flex-1 px-5 pb-20 pt-8 lg:px-20">
            {{ $slot }}
        </main>
    </div>
</div>
