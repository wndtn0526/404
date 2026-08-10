{{-- 조직 관리 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-274184 "page")
     좌측 400 조직도 트리에서 조직을 고르고, 우측 패널에서 그 조직의 정보를 본다.

     원본 실측(1920) — 좌 패널 400 · 우 패널 1096 · 사이 24 · 둘 다 반경 6 · 패딩 30
       좌: 제목 20 Bold lh30 (DS heading-2) · 조직 검색 340x32 · 제목 아래 30
           기준일 드롭다운 212x32 + 뷰 전환 32 두 개 + 추가 32 (사이 16)
           패널 폭 전체 구분선 · 트리 행 40 · 선택 행 면 Warm gray/100
           이름 14 Bold lh20 -0.2 (DS label-1) · '+ 조직 추가' 13 Medium (DS label-2) 우측
           펼침/접힘 표시 13x13 사각 테두리 Warm gray/400 안에 - / +
       우: 제목 20 Bold lh30 + 정보 변경 버튼 78x26 · 탭 32 + 패널 폭 전체 구분선
           섹션 제목 18 Bold lh27 -0.6 (DS headline-2 와 정확히 일치)
           섹션 사이 60 · 섹션 제목 → 첫 행 20 · 행 피치 36
           라벨 94 · 라벨↔값 16 · 두 열 사이 24 (컨텐츠·과정 상세와 같은 구조라
           x-detail-field 를 그대로 쓴다)

     ⚠️ 원본 LNB 는 우리 워크스페이스 셸과 다른 버전이다(커뮤니티·워크스페이스·화상 조직도·
        팀과 멤버·인사…). 셸은 우리 것을 그대로 쓰고 조직 관리만 메뉴에 더했다.
     ⚠️ 원본 상단 알약 탭이 7개다. 우리는 컨텐츠 관리·과정 관리·조직 관리 세 개를 쓰는
        partials/workspace-tabs 에 더했다.
     ⚠️ 원본 우측 탭은 조직 기본 · 변경 이력 · 주소 변경 · 조직장 관리 네 개다. 조직 기본만
        필드 화면이고, 나머지 셋은 전부 '제목 + 버튼 둘 + 표' 로 같은 모양이다
        (변경 이력 1002-274395 · 주소 변경 1002-274806 · 조직장 관리 1002-275173).
     ⚠️ 트리 연결선은 원본이 얇은 사각형 여러 개로 그려져 있다. 들여쓰기 + 짧은 가로선으로
        비슷하게만 맞췄다.
     ⚠️ 구성원은 요청대로 한 명(김기안)만 뒀다. 연락처는 눈에 보이는 더미 번호다 —
        실제 개인정보는 이 화면에 넣지 않는다.
     ⚠️ 화상조직도는 '막 만든 상태'(조직 하나 · 멤버 한 명)로 그려 뒀다. 이 화면은 팀이 몇 개
        있는 상태를 보여주므로 두 화면의 조직 목록이 다르다. 맞출지는 정하면 된다.
     ⚠️ 검색·기준일·뷰 전환·정보 변경·조직 추가는 아직 동작하지 않는다. --}}
@php
    /*
     * 좌측 조직도 트리 — Figma node 1002-274329 (안쪽 목록 1002-274353).
     * 중첩으로 적고 아래에서 평탄화한다. 연결선을 그리려면 각 행이 '조상 중 뒤에 형제가
     * 남은 단계' 를 알아야 하는데, 평평한 배열로는 그 정보가 안 나온다.
     *
     * ⚠️ 하위 조직(프론트엔드팀·백엔드팀·웹/앱 스쿼드·수강 운영팀)은 예시다. 원본 사이드바가
     *    그룹 > 법인 > 실 > 팀 > 스쿼드까지 다섯 단을 그리므로(node 1002-273882),
     *    연결선이 깊이마다 제대로 서는지 보려고 네 단까지 채웠다.
     */
    $orgTree = [
        ['name' => '청담원', 'current' => true, 'children' => [
            ['name' => '개발팀', 'children' => [
                ['name' => '프론트엔드팀', 'children' => [
                    ['name' => '웹 스쿼드'],
                    ['name' => '앱 스쿼드'],
                ]],
                ['name' => '백엔드팀'],
            ]],
            ['name' => '운영팀', 'children' => [
                ['name' => '수강 운영팀'],
            ]],
            ['name' => '콘텐츠팀'],
        ]],
    ];

    // 처음에 접혀 있는 노드. Alpine 이 이 배열을 그대로 받아 토글한다.
    $closedOrgs = ['운영팀'];

    /*
     * 평탄화 — 행마다 아래를 담는다.
     *   lines : 조상 단계별로 세로선을 그릴지 (그 조상 뒤에 형제가 남았는가)
     *   last  : 형제 중 마지막인가 (세로선을 행 중간까지만 그린다)
     *   path  : 조상 이름들 — 조상이 접히면 이 행을 숨긴다
     * 접힌 노드의 자식도 전부 그린다. 감추는 건 Alpine 이 한다(서버가 다시 그리지 않는다).
     */
    $flattenTree = function (array $nodes, array $lines = [], array $path = []) use (&$flattenTree) {
        $rows = [];
        $nodes = array_values($nodes);
        $lastIndex = count($nodes) - 1;

        foreach ($nodes as $i => $node) {
            $isLast = $i === $lastIndex;
            $hasChildren = ! empty($node['children']);

            $rows[] = [
                'name' => $node['name'],
                'depth' => count($lines),
                'lines' => $lines,
                'last' => $isLast,
                'children' => $hasChildren,
                'path' => $path,
                'current' => (bool) ($node['current'] ?? false),
            ];

            if ($hasChildren) {
                $rows = array_merge(
                    $rows,
                    $flattenTree($node['children'], [...$lines, ! $isLast], [...$path, $node['name']])
                );
            }
        }

        return $rows;
    };

    $treeRows = $flattenTree($orgTree);

    /*
     * 리스트 모드 — Figma node 1002-274090. 트리를 계단 없이 평평하게 편 표다.
     * 열: ID 100 · 조직 이름 140 · 조직 유형 80 · 조직 순차 80 = 400 (패널 폭 전체)
     *
     * ⚠️ 원본은 맨 위 조직의 조직 유형을 'Root' 로 적는다. 우리 조직 기본·상세는 청담원을
     *    '본부' 로 쓰고 있어서 그쪽에 맞췄다 — 한 화면에서 같은 값이 달리 보이면 안 된다.
     */
    $listRows = [];
    foreach ($treeRows as $i => $r) {
        $listRows[] = [
            'id' => str_pad((string) (101 + $i), 7, '0', STR_PAD_LEFT),
            'name' => $r['name'],
            'type' => $r['depth'] === 0 ? '본부' : '팀',
            'order' => '0',
        ];
    }

    // 선택된 조직(청담원)의 상세. 값은 예시다.
    $org = [
        'name' => '청담원',
        'basic' => [
            [['조직 이름', '청담원 · Cheongdamwon'], ['조직 유효 기간', '2021.08.01 -']],
            [['법인 이름', '청담원'], ['ID', '0000101']],
        ],
        'detail' => [
            // 루트라 상위 조직이 없다. 빈 값은 하이픈으로 나간다.
            [['상위 조직', null], ['조직 순차', '0']],
            [['국가', '대한민국'], ['근무지', '서울특별시 강남구']],
            [['조직 종류', '정규 조직'], ['조직 유형', '본부']],
            [['조직도 표시', '표시'], ['조직 계층', '01']],
            [['조직장', '김기안'], ['그룹 메일', 'cdw@cdw.workspace.io']],
            [['조직 주소', '서울특별시 강남구 청담동'], ['비고', '내용 없음']],
        ],
        'mission' => '요양보호 · 방문간호 교육 컨텐츠를 만들고 운영합니다.',
    ];

    // 조직장 — 요청대로 한 명만.
    $leader = [
        'name' => '김기안',
        'title' => '대표',
        'kind' => '대표',
        'since' => '2021.08.01 -',
        'mail' => 'kim@cdw.workspace.io',
        'phone' => '010 1234 5678',
    ];

    /*
     * 변경 이력 세 갈래 — 조직 정보 / 조직 업무는 '변경 이력' 탭, 주소는 '주소 변경' 탭,
     * 조직장은 '조직장 관리' 탭에 들어간다. 셋 다 같은 모양(제목 + 버튼 + 표)이다.
     *
     * ⚠️ 원본은 세 표 모두 같은 값을 2~3행 되풀이한 자리표시자다. 여기서는 조직 기본·상세와
     *    어긋나지 않게 실제 값 한 행씩만 뒀다.
     * 빈 배열로 두면 원본의 '아직 변경 이력이 없습니다.' 상태가 그대로 나온다
     * (node 1002-274623 · 1002-275001 · 1002-275368).
     */
    // 조직 정보 변경 이력 — 상위 조직이 없는 루트라 그 칸은 하이픈으로 나간다.
    $infoHistory = [
        ['from' => '2021.08.01', 'to' => null, 'name' => '청담원', 'parent' => null, 'note' => '내용 없음'],
    ];

    $missionHistory = [
        ['lang' => '한국어', 'text' => '요양보호 · 방문간호 교육 컨텐츠를 만들고 운영합니다.'],
        ['lang' => 'English', 'text' => 'We build and run care-worker education content.'],
    ];

    // 주소 변경 이력 — Figma node 1002-274806
    $addressHistory = [
        [
            'country' => '대한민국',
            'zip' => '06015',
            'address' => '서울특별시 강남구 청담동',
            'address_en' => null,
            'address_detail_en' => null,
            'from' => '2021.08.01',
            'to' => null,
        ],
    ];

    // 조직장 변경 이력 — Figma node 1002-275173. 구성원은 요청대로 한 명만.
    $leaderHistory = [
        [
            'name' => '김기안',
            'from' => '2021.08.01',
            'to' => null,
            'kind' => '대표',
            'employee_no' => '20210001',
            'title' => '대표',
            'note' => null,
        ],
    ];

    $sectionTitle = 'text-headline-2 font-bold leading-[27px] text-mono-black';
    // 모달 안 절 제목 16 Bold lh24 -0.6 · 필드 두 열(원본 열 사이 30 · 행 피치 78)
    $modalSection = 'text-body-1 font-bold leading-6 text-mono-black';
    $modalGrid = 'grid grid-cols-1 gap-x-[30px] gap-y-6 sm:grid-cols-2';
    // 섹션 제목 줄 — 제목은 좌우 30 안쪽, 표는 패널 폭 전체로 흘린다(원본 그대로)
    $histTitle = 'flex min-w-0 flex-wrap items-center justify-between gap-3 px-[30px]';
    $fieldGrid = 'grid grid-cols-1 gap-x-6 gap-y-4 lg:grid-cols-2';
    $iconBtn = 'flex size-8 shrink-0 items-center justify-center rounded-md border border-line-solid-normal bg-background-normal text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<x-layout title="조직 관리">
    <x-workspace-shell
        workspace="청담원"
        domain="cdw.workspace.io"
        user="김기안"
        has-alarm
        :rail="config('workspace.rail')"
        :items="config('workspace.items')"
        :footer-items="config('workspace.footer_items')"
        :scale="config('workspace.lnb_scale')"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => '홈', 'href' => url('/workspace')], ['label' => '조직 관리']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">조직 관리</h1>
            @include('partials.workspace-tabs', ['active' => 'orgs'])
        </x-slot:title>

        <x-slot:actions>
            <button type="button"
                    class="inline-flex items-center gap-2 text-label-1 text-mono-black transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                <span class="font-bold">기준일</span>
                <span>2021.08.01</span>
                <x-icon-caret-down class="size-3.5 shrink-0" />
            </button>
        </x-slot:actions>

        {{-- 주소 팝업 상태를 탭 상태와 같은 스코프에 둔다 — 표(행 클릭)와 모달이 둘 다 읽는다.
             row  : 클릭한 행의 원본 값 (되돌리기 기준)
             draft: 편집 중인 사본. row 와 달라지면 '저장' 이 살아난다.
             mode : view(상세 정보) | edit(상세 정보 수정)

             주소·조직장 팝업이 이 상태를 같이 쓴다. 한 번에 하나만 열리므로 나눠 둘 이유가 없다.
             필드가 서로 달라서 모달 본문만 따로 있다. --}}
        <div class="flex min-w-0 flex-col gap-6 xl:flex-row xl:items-start"
             x-data="{
                 tab: 'basic',
                 row: null,
                 draft: {},
                 mode: 'view',
                 openDetail(r, modal) {
                     this.row = r;
                     this.draft = { ...r };
                     this.mode = 'view';
                     this.$dispatch('open-modal', modal);
                 },
                 revert() { this.draft = { ...this.row }; this.mode = 'view'; },
                 get dirty() { return JSON.stringify(this.draft) !== JSON.stringify(this.row); },
                 v(key) { return this.row?.[key] || '-'; },
             }">

            {{-- ═══ 좌: 조직도 400 ═══ --}}
            <aside class="w-full min-w-0 shrink-0 rounded-lg bg-background-normal pb-5 xl:w-[400px]"
                   x-data="{
                       view: 'tree',
                       closed: @js($closedOrgs),
                       toggle(name) {
                           this.closed = this.closed.includes(name)
                               ? this.closed.filter(n => n !== name)
                               : [...this.closed, name];
                       },
                       isOpen(name) { return ! this.closed.includes(name); },
                       isHidden(path) { return path.some(p => this.closed.includes(p)); },
                   }">
                <h2 class="px-[30px] pt-[30px] text-heading-2 font-bold leading-[30px] text-mono-black">조직도</h2>

                <div class="px-[30px] pt-[30px]">
                    <x-input name="org_search" size="sm" icon="search" placeholder="조직 검색" />
                </div>

                {{-- 기준일 + 뷰 전환 + 조직 추가. 원본 폭 212 / 32·32 / 32, 사이 16 --}}
                <div class="flex min-w-0 items-center gap-4 px-[30px] pt-5">
                    <div class="min-w-0 flex-1">
                        <x-dropdown name="org_base_date" size="sm"
                                    :options="['2021.09.01' => '2021.09.01']" selected="2021.09.01" />
                    </div>

                    {{-- 트리 / 리스트 전환 — 원본은 아이콘만 있는 32짜리 두 칸이 맞붙어 있다(합 64).
                         고른 쪽이 Warm gray/200 으로 채워지고, 안 고른 쪽이 흰 면 + 테두리다
                         (트리 모드 1002-274941 · 리스트 모드 1002-274150 을 맞대 보고 확인).
                         DS x-segmented 는 라벨이 글자라서 아이콘을 넣을 수 없다.

                         ⚠️ 반경 4 가 바깥 모서리에만 있다. 모서리별 반경 변형은 DS 반경 단계
                            테스트의 허용 목록 밖이라, 바깥을 rounded-md + overflow-hidden 으로
                            잘랐다. 결과는 같다. (테스트가 파일 전체를 훑으므로 주석에도 그
                            클래스명을 적으면 안 된다.) --}}
                    <div class="inline-flex shrink-0 items-center overflow-hidden rounded-md" role="radiogroup" aria-label="보기 전환">
                        <button type="button" role="radio" @click="view = 'tree'"
                                x-bind:aria-checked="view === 'tree'" aria-label="트리로 보기"
                                class="flex size-8 items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary/40"
                                x-bind:class="view === 'tree'
                                    ? 'bg-warm-gray-200 text-mono-black'
                                    : 'border border-warm-gray-200 bg-background-normal text-label-alternative hover:text-label-normal'">
                            <x-icon-share class="size-6 -rotate-90" />
                        </button>
                        <button type="button" role="radio" @click="view = 'list'"
                                x-bind:aria-checked="view === 'list'" aria-label="리스트로 보기"
                                class="flex size-8 items-center justify-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-primary/40"
                                x-bind:class="view === 'list'
                                    ? 'bg-warm-gray-200 text-mono-black'
                                    : 'border border-warm-gray-200 bg-background-normal text-label-alternative hover:text-label-normal'">
                            <x-icon-list class="size-6" />
                        </button>
                    </div>

                    <button type="button" class="{{ $iconBtn }}" aria-label="조직 추가"
                            @click="$dispatch('open-modal', 'org-add')">
                        <x-icon-plus class="size-5" />
                    </button>
                </div>

                <div class="mt-5 h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- 트리 — Figma node 1002-274353 · 깊이는 1002-273882(4차 펼침) 실측
                     행 40 · 선택 행 Warm gray/100 · 이름 14 (자식 있으면 Bold · 잎이면 Medium)
                     단계 칸 26 → 그 뒤 20 · 세로선 x+6 · 잎 엘보 가로선 8 (행 중간)
                     마지막 자식은 세로선을 행 중간까지만 그린다
                     토글 13x13 반경 1 — 펼침: 테두리 Warm gray/400 + 마이너스
                                        접힘: 면 Warm gray/700 + 흰 플러스
                     '+ 조직 추가' 는 행에 올렸을 때 우측에 보인다

                     ⚠️ 접기는 Alpine 이 감추는 방식이다(서버가 다시 그리지 않는다). x-show 대신
                        :class 로 감춘다 — x-show 는 Alpine 이 그리기를 프레임에 미뤄서
                        헤드리스 확인이 안 된다.
                     ⚠️ 행을 눌러도 우측 상세는 청담원 그대로다. 다른 조직의 데이터가 없다.
                     ⚠️ 이름 굵기는 '자식 있으면 Bold' 로 뒀다. 원본은 같은 단계인 GPRO 웹툰·GPRO 페이가
                        Bold 인데 GPRO 만 Medium 이다(1002-274353 · 1002-273882 둘 다 그렇다).
                        형제끼리 굵기가 갈리면 읽을 규칙이 없어서 한쪽으로 맞췄다. --}}
                <ul class="pt-4" x-bind:class="{ 'hidden': view !== 'tree' }">
                    @foreach ($treeRows as $row)
                        <li @if ($row['path']) :class="{ 'hidden': isHidden(@js($row['path'])) }" @endif>
                            <div @class([
                                'group flex h-10 min-w-0 items-center pl-[30px] pr-[30px] transition-colors',
                                'bg-warm-gray-100' => $row['current'],
                                'hover:bg-fill-alternative' => ! $row['current'],
                            ])>
                                {{-- 조상 단계 — 뒤에 형제가 남은 단계만 세로선이 계속 내려간다.
                                     ⚠️ 단계 폭이 균일하지 않다. 첫 단만 26 이고 그 뒤로는 20 씩이다
                                        (원본 4차 펼침 1002-273882 실측: 토글 좌가 30 · 56 · 76 · 96).
                                        전부 26 으로 두면 깊어질수록 원본과 벌어진다. --}}
                                @foreach ($row['lines'] as $level => $hasLine)
                                    <span @class([
                                        'relative h-10 shrink-0',
                                        'w-[26px]' => $level === 0,
                                        'w-[20px]' => $level > 0,
                                    ]) aria-hidden="true">
                                        @if ($hasLine)
                                            <span class="absolute left-[6px] top-0 h-full w-px bg-warm-gray-400"></span>
                                        @endif
                                    </span>
                                @endforeach

                                {{-- 자기 단계 — 토글(자식 있음) 또는 엘보(잎) --}}
                                @if ($row['children'])
                                    <span class="relative h-10 w-[21px] shrink-0">
                                        @if ($row['depth'] > 0)
                                            {{-- 토글을 비켜 위/아래로 나뉜다(원본 그대로) --}}
                                            <span class="absolute left-[6px] top-0 h-[14px] w-px bg-warm-gray-400" aria-hidden="true"></span>
                                            @unless ($row['last'])
                                                <span class="absolute left-[6px] top-[27px] h-[13px] w-px bg-warm-gray-400" aria-hidden="true"></span>
                                            @endunless
                                        @endif

                                        <button type="button"
                                                @click="toggle(@js($row['name']))"
                                                x-bind:aria-expanded="isOpen(@js($row['name']))"
                                                aria-label="{{ $row['name'] }} 하위 조직 접기/펴기"
                                                class="absolute left-0 top-[14px] flex size-[13px] items-center justify-center rounded-xs focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                                x-bind:class="isOpen(@js($row['name']))
                                                    ? 'border border-warm-gray-400 text-warm-gray-400'
                                                    : 'bg-warm-gray-700 text-white'">
                                            <span class="absolute h-px w-[7px] bg-current" aria-hidden="true"></span>
                                            <span class="absolute h-[7px] w-px bg-current" aria-hidden="true"
                                                  x-bind:class="isOpen(@js($row['name'])) && 'hidden'"></span>
                                        </button>
                                    </span>
                                @else
                                    {{-- 잎은 세로선 + 엘보만 있고 토글이 없다. 원본 이름은 세로선에서 17
                                         (= 칸 시작에서 23) 인데, 같은 그룹에 토글 있는 형제가 섞이면 이름
                                         왼끝이 2 어긋난다. 토글 칸과 같은 21 로 맞춰 나란히 뒀다. --}}
                                    <span class="relative h-10 w-[21px] shrink-0" aria-hidden="true">
                                        <span @class([
                                            'absolute left-[6px] top-0 w-px bg-warm-gray-400',
                                            'h-[21px]' => $row['last'],
                                            'h-full' => ! $row['last'],
                                        ])></span>
                                        <span class="absolute left-[7px] top-[20px] h-px w-2 bg-warm-gray-400"></span>
                                    </span>
                                @endif

                                <a href="#" @class([
                                        'min-w-0 flex-1 truncate pl-2 text-label-1 leading-5 text-mono-black focus:outline-none focus-visible:underline',
                                        'font-bold' => $row['children'],
                                        'font-medium' => ! $row['children'],
                                    ])
                                   @if ($row['current']) aria-current="true" @endif>
                                    {{ $row['name'] }}
                                </a>

                                {{-- 원본은 행에 올렸을 때 보인다 --}}
                                <button type="button"
                                        @click="$dispatch('open-modal', 'org-add')"
                                        class="shrink-0 pl-2 text-label-2 font-medium leading-5 text-mono-black opacity-0 transition-opacity focus:outline-none group-hover:opacity-100 focus-visible:opacity-100">
                                    + 조직 추가
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- ── 리스트 모드 ── Figma node 1002-274090
                     트리를 계단 없이 평평하게 편 표다. 계층은 안 보이고 조직 유형 열로 구분한다.

                     원본 실측 — 행 28 (헤더도 28) · 글자 13
                       ID 100 | 조직 이름 140 | 조직 유형 80 | 조직 순차 80 = 400 (패널 폭 전체)
                       첫 칸 좌 패딩 30 (패널 여백에 맞춘 것) · 나머지 16
                       표 아래 패널 폭 구분선 → '엑셀로 저장' 우측

                     원본 주석 그대로 — 엑셀 저장은 쓰는 일이 드물어서 위 툴바가 아니라 아래 뒀다.

                     ⚠️ 접힘 상태와 상관없이 모든 조직이 나온다(원본도 그렇다).
                     ⚠️ 엑셀로 저장은 아직 동작하지 않는다. 내려받기가 붙으면 권한 확인 후
                        스트리밍한다. --}}
                <div x-bind:class="{ 'hidden': view !== 'list' }">
                    <x-table dense min-width="400px" class="rounded-none border-x-0 border-t-0">
                        <x-table.head dense :columns="[
                            ['label' => 'ID', 'width' => '100px'],
                            ['label' => '조직 이름', 'width' => '140px'],
                            ['label' => '조직 유형', 'width' => '80px'],
                            ['label' => '조직 순차'],
                        ]" />
                        <tbody>
                            @forelse ($listRows as $row)
                                <x-table.row>
                                    <x-table.cell dense tone="muted" nowrap>
                                        <span class="tabular-nums">{{ $row['id'] }}</span>
                                    </x-table.cell>
                                    <x-table.cell dense tone="strong" nowrap>{{ $row['name'] }}</x-table.cell>
                                    <x-table.cell dense tone="muted" nowrap>{{ $row['type'] }}</x-table.cell>
                                    <x-table.cell dense tone="muted" nowrap>
                                        <span class="tabular-nums">{{ $row['order'] }}</span>
                                    </x-table.cell>
                                </x-table.row>
                            @empty
                                <x-table.empty :colspan="4">조직이 없습니다.</x-table.empty>
                            @endforelse
                        </tbody>
                    </x-table>

                    <div class="mt-[30px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                    <div class="flex justify-end px-[30px] pt-4">
                        <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                    </div>
                </div>
            </aside>

            {{-- ═══ 우: 조직 상세 ═══ --}}
            <div class="min-w-0 flex-1 rounded-lg bg-background-normal pb-[30px]">
                <div class="flex min-w-0 flex-wrap items-center gap-3 px-[30px] pt-[30px]">
                    <h2 class="min-w-0 truncate text-heading-2 font-bold leading-[30px] text-mono-black">{{ $org['name'] }}</h2>
                    {{-- 원본 78x26 — DS 버튼 sm 은 40 이라 한 단계 크다 --}}
                    <x-button variant="outline" size="sm" icon="pencil">정보 변경</x-button>
                </div>

                <div class="px-[30px] pt-[30px]">
                    <x-tabs
                        name="org_detail_tab"
                        x-model="tab"
                        :options="['basic' => '조직 기본', 'history' => '변경 이력', 'address' => '주소 변경', 'leader' => '조직장 관리']"
                        selected="basic"
                        accent="strong"
                    />
                </div>

                {{-- ── 조직 기본 ── --}}
                <div x-show="tab === 'basic'" class="px-[30px] pt-10">
                    <h3 class="{{ $sectionTitle }}">기본 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-5">
                        @foreach ($org['basic'] as $row)
                            @foreach ($row as [$label, $value])
                                <x-detail-field :label="$label" :value="$value" />
                            @endforeach
                        @endforeach
                    </dl>

                    <h3 class="{{ $sectionTitle }} pt-[60px]">상세 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-5">
                        @foreach ($org['detail'] as $row)
                            @foreach ($row as [$label, $value])
                                <x-detail-field :label="$label" :value="$value" />
                            @endforeach
                        @endforeach
                    </dl>

                    <h3 class="{{ $sectionTitle }} pt-[60px]">조직 주요 업무</h3>
                    <p class="pt-5 text-label-1 font-medium leading-5 text-mono-black">{{ $org['mission'] }}</p>

                    <h3 class="{{ $sectionTitle }} pt-[60px]">조직장 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-5">
                        {{-- 이름 칸만 원본에 프로필 18 이 붙어 있다 --}}
                        <x-detail-field label="이름">
                            <span class="flex min-w-0 items-center gap-1.5">
                                <x-thumbnail :name="$leader['name']" size="xs" shape="circle" class="size-[18px]" />
                                <span class="truncate">{{ $leader['name'] }}</span>
                            </span>
                        </x-detail-field>
                        <x-detail-field label="직책" :value="$leader['title']" />

                        <x-detail-field label="구분" :value="$leader['kind']" />
                        <x-detail-field label="시작일" :value="$leader['since']" />

                        <x-detail-field label="회사 메일" :value="$leader['mail']" />
                        <x-detail-field label="연락처" :value="$leader['phone']" />
                    </dl>
                </div>

                {{-- ── 조직장 관리 ── Figma node 1002-275173
                     '조직장 목록' 이 아니라 조직장 변경 이력이다. 변경 이력 탭과 같은 모양이다.

                     원본 실측 — 섹션 제목 18 Bold lh27 (DS headline-2) · 제목 아래 20
                       표 행 56 · 84 | 160 | 110 | 110 | 120 | 110 | 160 | 242 = 1096
                       이름 칸만 프로필 셀 — 아바타 32 + 간격 10 + 이름 13 Bold

                     ⚠️ 체크박스 열이 원본 84 인데 DS x-table 은 48(w-12 px-5)로 고정이다.
                     ⚠️ 종료일·비고는 아직 값이 없다. 빈 값은 하이픈으로 나간다. --}}
                <div x-show="tab === 'leader'" x-cloak class="pt-10">
                    <div class="{{ $histTitle }}">
                        <h3 class="{{ $sectionTitle }}">조직장 변경 이력</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                            <x-button variant="outline" size="sm" icon="plus">변경 이력 추가</x-button>
                        </div>
                    </div>

                    <div class="pt-5">
                        <x-table selectable min-width="1060px" class="rounded-none border-x-0">
                            <x-table.head
                                selectable
                                :all-ids="collect($leaderHistory)->pluck('employee_no')->all()"
                                :columns="[
                                    ['label' => '이름', 'width' => '160px'],
                                    ['label' => '시작일', 'width' => '110px'],
                                    ['label' => '종료일', 'width' => '110px'],
                                    ['label' => '구분', 'width' => '120px'],
                                    ['label' => '사번', 'width' => '110px'],
                                    ['label' => '직책', 'width' => '160px'],
                                    ['label' => '비고'],
                                ]"
                            />
                            <tbody>
                                @forelse ($leaderHistory as $row)
                                    {{-- 주소 변경과 같다 — 행 전체가 상세 팝업을 연다(node 1002-280870). --}}
                                    <x-table.row selectable :value="$row['employee_no']" class="relative">
                                        <x-table.cell tone="strong" nowrap>
                                            <span class="flex min-w-0 items-center gap-2.5">
                                                <x-thumbnail :name="$row['name']" size="sm" shape="circle" />
                                                <button type="button" @click="openDetail(@js($row), 'leader-detail')"
                                                        class="truncate text-left after:absolute after:inset-0 focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4">
                                                    {{ $row['name'] }}
                                                </button>
                                            </span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['from'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['to'] ?? '-' }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>{{ $row['kind'] }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['employee_no'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>{{ $row['title'] }}</x-table.cell>
                                        <x-table.cell tone="muted">{{ $row['note'] ?? '-' }}</x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.empty :colspan="8" />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>

                {{-- ── 변경 이력 ── Figma node 1002-274395
                     표 두 개다. 섹션 제목은 좌우 30 안쪽인데 표는 패널 폭 전체로 흘린다(원본 그대로).

                     원본 실측 — 섹션 제목 18 Bold lh27 (DS headline-2) · 제목 아래 20
                       표 행 56 (DS x-table 행과 같다) · 첫 열은 체크박스
                       조직 정보 변경 이력  84 | 160 | 160 | 200 | 200 | 292
                       조직 업무 변경 이력  84 | 160 | 852
                       섹션 사이 내부 폭 구분선(1036) · 버튼 30 (엑셀로 저장 93 · 변경 이력 추가 106)

                     ⚠️ 체크박스 열이 원본 84 인데 DS x-table 은 48(w-12 px-5)로 고정이다.
                     ⚠️ 버튼이 원본 30 인데 DS 버튼 sm 은 40 이라 한 단계 크다.
                     ⚠️ 원본은 두 표가 각각 2행인데 같은 값을 되풀이한 자리표시자다. 여기서는
                        조직 정보는 1행(만들어진 기록 하나)으로, 조직 업무는 언어별 2행으로 뒀다 —
                        상세 정보의 조직 유효 기간(2021.08.01 -)·주요 업무와 어긋나지 않게.
                     ⚠️ 엑셀로 저장 · 변경 이력 추가는 아직 동작하지 않는다. 내려받기가 붙으면
                        권한 확인 후 스트리밍한다. --}}
                <div x-show="tab === 'history'" x-cloak class="pt-10">
                    {{-- 조직 정보 변경 이력 --}}
                    <div class="{{ $histTitle }}">
                        <h3 class="{{ $sectionTitle }}">조직 정보 변경 이력</h3>
                        {{-- 입력할 값이 많아서 팝업이 아니라 페이지로 보낸다(node 1002-274589) --}}
                        <x-button variant="outline" size="sm" icon="plus" href="{{ url('/orgs/history') }}">변경 이력 추가</x-button>
                    </div>

                    <div class="pt-5">
                        <x-table selectable min-width="900px" class="rounded-none border-x-0">
                            <x-table.head
                                selectable
                                :all-ids="collect($infoHistory)->pluck('from')->all()"
                                :columns="[
                                    ['label' => '조직 시작일', 'width' => '160px'],
                                    ['label' => '조직 종료일', 'width' => '160px'],
                                    ['label' => '조직 이름', 'width' => '200px'],
                                    ['label' => '상위 조직 이름', 'width' => '200px'],
                                    ['label' => '비고'],
                                ]"
                            />
                            <tbody>
                                @forelse ($infoHistory as $row)
                                    <x-table.row selectable :value="$row['from']">
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['from'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['to'] ?? '-' }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="strong" nowrap>{{ $row['name'] }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>{{ $row['parent'] ?? '-' }}</x-table.cell>
                                        <x-table.cell tone="muted">{{ $row['note'] }}</x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.empty :colspan="6" />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>

                    <div class="mx-[30px] mt-10 h-px bg-warm-gray-100" aria-hidden="true"></div>

                    {{-- 조직 업무 변경 이력 --}}
                    <div class="{{ $histTitle }} pt-10">
                        <h3 class="{{ $sectionTitle }}">조직 업무 변경 이력</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                            <x-button variant="outline" size="sm" icon="plus">변경 이력 추가</x-button>
                        </div>
                    </div>

                    <div class="pt-5">
                        <x-table selectable min-width="720px" class="rounded-none border-x-0">
                            <x-table.head
                                selectable
                                :all-ids="collect($missionHistory)->pluck('lang')->all()"
                                :columns="[
                                    ['label' => '언어', 'width' => '160px'],
                                    ['label' => '주요 업무'],
                                ]"
                            />
                            <tbody>
                                @forelse ($missionHistory as $row)
                                    <x-table.row selectable :value="$row['lang']">
                                        <x-table.cell tone="muted" nowrap>{{ $row['lang'] }}</x-table.cell>
                                        <x-table.cell tone="strong">{{ $row['text'] }}</x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.empty :colspan="3" />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>

                {{-- ── 주소 변경 ── Figma node 1002-274806
                     원본 실측 — 표 84 | 120 | 100 | 252 | 160 | 160 | 110 | 110 = 1096
                       상세 주소가 252 로 가장 넓다. 영문 주소·영문 상세 주소는 따로 두 칸이다 —
                       원본 주석대로 내용이 길어질 수 있어 줄을 나눠 뒀다.

                     ⚠️ 체크박스 열이 원본 84 인데 DS x-table 은 48(w-12 px-5)로 고정이다.
                     ⚠️ 영문 주소는 아직 값이 없다. 빈 값은 하이픈으로 나간다. --}}
                <div x-show="tab === 'address'" x-cloak class="pt-10">
                    <div class="{{ $histTitle }}">
                        <h3 class="{{ $sectionTitle }}">주소 변경 이력</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                            <x-button variant="outline" size="sm" icon="plus">변경 이력 추가</x-button>
                        </div>
                    </div>

                    <div class="pt-5">
                        <x-table selectable min-width="1060px" class="rounded-none border-x-0">
                            <x-table.head
                                selectable
                                :all-ids="collect($addressHistory)->pluck('from')->all()"
                                :columns="[
                                    ['label' => '국가', 'width' => '120px'],
                                    ['label' => '우편 번호', 'width' => '100px'],
                                    ['label' => '상세 주소', 'width' => '252px'],
                                    ['label' => '영문 주소', 'width' => '160px'],
                                    ['label' => '영문 상세 주소', 'width' => '160px'],
                                    ['label' => '시작일', 'width' => '110px'],
                                    ['label' => '종료일'],
                                ]"
                            />
                            <tbody>
                                @forelse ($addressHistory as $row)
                                    {{-- 행 전체가 상세 팝업을 연다. <tr> 을 버튼으로 감쌀 수 없어서
                                         상세 주소 셀의 버튼을 행 전체로 늘렸다(after:inset-0).
                                         체크박스는 x-table.row 안에서 z-10 이라 이 면 위에 남는다.
                                         컨텐츠·과정 목록은 같은 자리에 <a> 를 쓴다 — 거기는 페이지로
                                         가고 여기는 팝업이라 <button> 이다. --}}
                                    <x-table.row selectable :value="$row['from']" class="relative">
                                        <x-table.cell tone="muted" nowrap>{{ $row['country'] }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['zip'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="strong">
                                            <button type="button" @click="openDetail(@js($row), 'address-detail')"
                                                    class="text-left after:absolute after:inset-0 focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4">
                                                {{ $row['address'] }}
                                            </button>
                                        </x-table.cell>
                                        <x-table.cell tone="muted">{{ $row['address_en'] ?? '-' }}</x-table.cell>
                                        <x-table.cell tone="muted">{{ $row['address_detail_en'] ?? '-' }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['from'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['to'] ?? '-' }}</span>
                                        </x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.empty :colspan="8" />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>
            </div>

            {{-- ═══ 주소 상세 팝업 ═══ Figma node 1002-280870 "열 클릭 시 등장하는 팝업"
                 (읽기 1002-280923 · 수정 1002-280877 · 저장 활성 1002-280901)

                 원본 주석 그대로 — 표에서 바로 고치면 실수로 값이 바뀌니까, 행을 누르면 먼저
                 '보기' 팝업이 뜨고 '정보 수정'을 눌러야 입력 칸으로 바뀐다. 아무것도 안 고치면
                 '저장'은 비활성이다.

                 원본 실측 — 폭 720 · 반경 6 · 패딩 30
                   제목 20 Bold lh30 -1 (DS heading-2) → 30 → 절 제목 16 Bold lh24 -0.6 (DS body-1)
                   보기: 라벨 94 + 값 · 줄 20 · 행 피치 36 · 열 사이 24 (DS $fieldGrid 와 같다)
                   수정: 칸 315x54 · 열 사이 30 · 행 피치 78 (조직 추가 모달과 같다)
                   절 사이 40 · 카드 폭 구분선 → 25 → 버튼 120x36 (사이 16 · 우측)
                   보기 [정보 수정(선) · 확인(면)] / 수정 [취소(선) · 저장(면)]

                 ⚠️ 원본 화면은 인사 정보(이름·소속·사번…)를 예로 들고 있다. 여기 붙는 건
                    주소 변경 이력이라 필드를 그 표의 열로 바꿨다 — 팝업 골격만 가져온 것이다.
                 ⚠️ 원본 비활성 '저장'은 면 Warm gray/200 인데 DS Disabled 면은 Warm gray/100 이다.
                    한 화면 때문에 DS 토큰을 바꾸지 않았다. 글자색(Warm gray/400)은 같다.
                 ⚠️ 저장 엔드포인트가 없다. '저장'은 아직 아무 일도 하지 않는다.
                    붙일 때는 POST + CSRF 로 보낸다. --}}
            <x-modal name="address-detail" max-width="max-w-[720px]" scroll close-button>
                <h2 class="pr-10 text-heading-2 font-bold leading-[30px] text-mono-black"
                    x-text="mode === 'view' ? '상세 정보' : '상세 정보 수정'"></h2>

                {{-- ── 보기 ── --}}
                <div x-bind:class="{ 'hidden': mode !== 'view' }">
                    <h3 class="{{ $modalSection }} pt-[30px]">기본 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-6">
                        <x-detail-field label="국가"><span x-text="v('country')"></span></x-detail-field>
                        <x-detail-field label="우편 번호"><span class="tabular-nums" x-text="v('zip')"></span></x-detail-field>
                        <x-detail-field label="상세 주소"><span x-text="v('address')"></span></x-detail-field>
                        <x-detail-field label="영문 주소"><span x-text="v('address_en')"></span></x-detail-field>
                        <x-detail-field label="영문 상세 주소"><span x-text="v('address_detail_en')"></span></x-detail-field>
                    </dl>

                    <h3 class="{{ $modalSection }} pt-10">기타 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-6">
                        <x-detail-field label="시작일"><span class="tabular-nums" x-text="v('from')"></span></x-detail-field>
                        <x-detail-field label="종료일"><span class="tabular-nums" x-text="v('to')"></span></x-detail-field>
                    </dl>
                </div>

                {{-- ── 수정 ── --}}
                <div x-bind:class="{ 'hidden': mode !== 'edit' }">
                    <h3 class="{{ $modalSection }} pt-[30px]">기본 정보</h3>
                    <div class="{{ $modalGrid }} pt-6">
                        <x-input label="국가" size="sm" x-model="draft.country" />
                        <x-input label="우편 번호" size="sm" x-model="draft.zip" />
                        <x-input label="상세 주소" size="sm" x-model="draft.address" />
                        <x-input label="영문 주소" size="sm" placeholder="영문 주소 입력" x-model="draft.address_en" />
                        <x-input label="영문 상세 주소" size="sm" placeholder="영문 상세 주소 입력" x-model="draft.address_detail_en" />
                    </div>

                    <h3 class="{{ $modalSection }} pt-10">기타 정보</h3>
                    <div class="{{ $modalGrid }} pt-6">
                        <x-input label="시작일" size="sm" x-model="draft.from" />
                        <x-input label="종료일" size="sm" placeholder="종료일 입력" x-model="draft.to" />
                    </div>
                </div>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4" x-bind:class="{ 'hidden': mode !== 'view' }">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="mode = 'edit'">정보 수정</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]" @click="open = false">확인</x-button>
                    </div>
                    <div class="ml-auto flex flex-wrap items-center gap-4" x-bind:class="{ 'hidden': mode !== 'edit' }">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="revert()">취소</x-button>
                        {{-- 아무것도 안 고쳤으면 누를 수 없다(원본 그대로) --}}
                        <x-button variant="primary" size="sm" class="w-[120px]" x-bind:disabled="! dirty">저장</x-button>
                    </div>
                </x-slot:footer>
            </x-modal>

            {{-- ═══ 조직장 상세 팝업 ═══ Figma node 1002-280870 "열 클릭 시 등장하는 팝업"
                 주소 팝업과 같은 골격이다(보기 1002-280923 · 수정 1002-280877). 필드만 다르다.
                 상태(row·draft·mode)는 위 x-data 를 같이 쓴다 — 한 번에 하나만 열린다.

                 ⚠️ 표의 이름 칸에는 아바타가 붙지만 원본 팝업의 이름은 글자만이다. 그대로 뒀다.
                 ⚠️ 사번은 원본이 '기타 정보' 에 두는데 여기서는 사람을 가리키는 값이라 '기본 정보'
                    에 뒀다. 기타 정보는 기간·비고만 남겼다.
                 ⚠️ 저장 엔드포인트가 없다. '저장'은 아직 아무 일도 하지 않는다. --}}
            <x-modal name="leader-detail" max-width="max-w-[720px]" scroll close-button>
                <h2 class="pr-10 text-heading-2 font-bold leading-[30px] text-mono-black"
                    x-text="mode === 'view' ? '상세 정보' : '상세 정보 수정'"></h2>

                {{-- ── 보기 ── --}}
                <div x-bind:class="{ 'hidden': mode !== 'view' }">
                    <h3 class="{{ $modalSection }} pt-[30px]">기본 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-6">
                        <x-detail-field label="이름"><span x-text="v('name')"></span></x-detail-field>
                        <x-detail-field label="사번"><span class="tabular-nums" x-text="v('employee_no')"></span></x-detail-field>
                        <x-detail-field label="직책"><span x-text="v('title')"></span></x-detail-field>
                        <x-detail-field label="구분"><span x-text="v('kind')"></span></x-detail-field>
                    </dl>

                    <h3 class="{{ $modalSection }} pt-10">기타 정보</h3>
                    <dl class="{{ $fieldGrid }} pt-6">
                        <x-detail-field label="시작일"><span class="tabular-nums" x-text="v('from')"></span></x-detail-field>
                        <x-detail-field label="종료일"><span class="tabular-nums" x-text="v('to')"></span></x-detail-field>
                        <x-detail-field label="비고"><span x-text="v('note')"></span></x-detail-field>
                    </dl>
                </div>

                {{-- ── 수정 ── --}}
                <div x-bind:class="{ 'hidden': mode !== 'edit' }">
                    <h3 class="{{ $modalSection }} pt-[30px]">기본 정보</h3>
                    <div class="{{ $modalGrid }} pt-6">
                        <x-input label="이름" size="sm" x-model="draft.name" />
                        <x-input label="사번" size="sm" x-model="draft.employee_no" />
                        <x-dropdown label="직책" size="sm"
                                    :options="['대표' => '대표', '본부장' => '본부장', '실장' => '실장', '팀장' => '팀장']"
                                    x-model="draft.title" />
                        <x-dropdown label="구분" size="sm"
                                    :options="['대표' => '대표', '겸직' => '겸직', '직무대행' => '직무대행']"
                                    x-model="draft.kind" />
                    </div>

                    <h3 class="{{ $modalSection }} pt-10">기타 정보</h3>
                    <div class="{{ $modalGrid }} pt-6">
                        <x-input label="시작일" size="sm" x-model="draft.from" />
                        <x-input label="종료일" size="sm" placeholder="종료일 입력" x-model="draft.to" />
                        <x-input label="비고" size="sm" placeholder="내용 입력" x-model="draft.note" />
                    </div>
                </div>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4" x-bind:class="{ 'hidden': mode !== 'view' }">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="mode = 'edit'">정보 수정</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]" @click="open = false">확인</x-button>
                    </div>
                    <div class="ml-auto flex flex-wrap items-center gap-4" x-bind:class="{ 'hidden': mode !== 'edit' }">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="revert()">취소</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]" x-bind:disabled="! dirty">저장</x-button>
                    </div>
                </x-slot:footer>
            </x-modal>
        </div>

        {{-- ═══ 새 조직 추가 모달 ═══ Figma node 1002-273618
             (접힌 상태 1002-273620 · 펼친 상태 1002-273633)

             툴바의 + 와 트리 각 행의 '+ 조직 추가' 가 이 모달을 연다.

             원본 실측 — 폭 720 · 반경 6 · 패딩 30 · 제목 20 Bold lh30 -1 (DS heading-2 와 일치)
               섹션 제목 16 Bold lh24 -0.6 (DS body-1 과 정확히 일치) · 제목 아래 24
               필드 315x54 · 열 사이 30 (315+30+315 = 660 = 내부 폭) · 행 피치 78
               기본 정보 뒤 내부 폭 구분선(660) → 30 → 상세 정보 제목 + 접기 화살표
               모달 폭 구분선(720) → 25 → 버튼 120x36 (사이 16 · 우측 정렬) → 30

             ⚠️ 상세 정보는 접히는 절이다. 원본 기본 상태가 접힘이라 그대로 뒀다.
             ⚠️ 원본 제목 앞 📄 이모지는 빼기로 한 규칙대로 넣지 않았다.
             ⚠️ 상위 조직 · 조직 계층은 원본이 disabled 다(면 Warm gray/100 · 글자 Warm gray/600).
                트리에서 고른 자리로 정해지는 값이라 손입력이 아니다.
             ⚠️ 생성일은 원본이 날짜 드롭다운이다. DS 에는 x-datepicker 가 있지만 높이가 40 이라
                32 짜리 필드 줄과 어긋난다. 지금은 드롭다운으로 뒀다 — 날짜 선택이 실제로 필요해지면
                x-datepicker 로 바꾸고 그 줄만 높이를 맞춘다.
             ⚠️ 원본 버튼은 '닫기 / 추가' 다(법인·팀 모달은 '취소 / 추가').
             ⚠️ 저장 엔드포인트가 없다. '추가'는 모달만 닫는다. --}}
        <x-modal name="org-add" title="새 조직 추가" max-width="max-w-[720px]" scroll close-button>
            <div x-data="{ detail: false }">
                <h3 class="text-body-1 font-bold leading-6 text-mono-black">기본 정보 (필수)</h3>

                <div class="{{ $modalGrid }} pt-6">
                    <x-input label="조직 이름 (한글)" name="org_name_ko" size="sm" placeholder="조직 이름 입력" />
                    <x-dropdown label="생성일" name="org_created_at" size="sm"
                                :options="[now()->format('Y.m.d') => now()->format('Y.m.d')]"
                                :selected="now()->format('Y.m.d')" />
                </div>

                <div class="mt-[30px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- 상세 정보 — 접었다 펼친다. 원본 기본 상태는 접힘이다. --}}
                <button type="button" @click="detail = ! detail"
                        class="mt-[30px] flex min-w-0 items-center gap-2 text-left focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4"
                        x-bind:aria-expanded="detail">
                    <span class="text-body-1 font-bold leading-6 text-mono-black">상세 정보 (선택)</span>
                    <x-icon-chevron-down class="size-3.5 shrink-0 transition-transform" x-bind:class="detail && '!rotate-180'" />
                </button>

                <div x-show="detail" x-cloak class="{{ $modalGrid }} pt-6">
                    <x-dropdown label="법인 이름" name="org_corp" size="sm"
                                :options="['청담원' => '청담원']" selected="청담원" />
                    {{-- 트리에서 고른 자리로 정해진다 --}}
                    <x-input label="상위 조직" size="sm" value="청담원" disabled />

                    <x-input label="조직 이름 (영어)" name="org_name_en" size="sm" placeholder="조직 영어 이름 입력" />
                    <x-input label="조직 순차" name="org_order" type="number" size="sm" value="0" min="0" />

                    <x-dropdown label="국가" name="org_country" size="sm"
                                :options="['대한민국' => '대한민국']" selected="대한민국" />
                    <x-dropdown label="근무지" name="org_place" size="sm"
                                :options="['서울특별시 강남구' => '서울특별시 강남구']" selected="서울특별시 강남구" />

                    <x-dropdown label="조직 종류" name="org_kind" size="sm"
                                :options="['정규 조직' => '정규 조직', '임시 조직' => '임시 조직']"
                                placeholder="조직 종류 선택" />
                    <x-dropdown label="조직 유형" name="org_type" size="sm"
                                :options="['본부' => '본부', '실' => '실', '팀' => '팀', '스쿼드' => '스쿼드']"
                                placeholder="조직 유형 선택" />

                    <x-dropdown label="조직도 표시" name="org_visible" size="sm"
                                :options="['표시' => '표시', '미표시' => '미표시']" selected="표시" />
                    {{-- 상위 조직이 정해지면 따라 정해진다 --}}
                    <x-input label="조직 계층" size="sm" value="02" disabled />

                    <x-input label="그룹 메일" name="org_mail" size="sm" placeholder="그룹 메일 입력" />
                    <x-input label="비고" name="org_note" size="sm" placeholder="내용 입력" />
                </div>
            </div>

            <x-slot:footer>
                {{-- DS 모달 푸터는 gap-3 좌측 정렬이다. 원본은 우측 정렬 · 사이 16 이라 감싼다. --}}
                <div class="flex w-full flex-wrap items-center justify-end gap-4">
                    <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">닫기</x-button>
                    <x-button variant="primary" size="sm" class="w-[120px]" @click="open = false">추가</x-button>
                </div>
            </x-slot:footer>
        </x-modal>
    </x-workspace-shell>
</x-layout>
