{{-- 컨텐츠 관리 — Figma 워크스페이스 화면 (Lnsej46BaxtyKq3rhssFH3 · node 1-299)
     크롬(LNB·GNB·헤더)은 <x-workspace-shell> 이 갖고 있다. 이 파일은 화면 고유의
     브레드크럼·타이틀·탭·기준일과 본문 표를 채운다.

     ⚠️ 표는 Figma 에 디자인이 없다. 원본 node 1-299 의 본문은 비어 있다.
        DS 표 컴포넌트(Data Tables 1002:523369)로 조립하고 컬럼은 컨텐츠 관리에
        필요할 만한 것으로 잡았다. 디자인이 나오면 컬럼·정렬·상태값을 맞춰야 한다.

     ⚠️ 행 데이터는 뷰에 박아둔 예시다. DB 에서 오지 않는다. 모델·서비스가 붙으면
        컨트롤러에서 넘겨받는다(로직은 Service Layer). --}}
@php
    // 영상 분·초는 값이 있으면 두 자리 + 단위(00분 / 00초), 없으면 하이픈.
    // em dash 는 쓰지 않는다.
    $unit = fn (?string $v, string $suffix) => filled($v) ? str_pad($v, 2, '0', STR_PAD_LEFT).$suffix : '-';

    $rows = [
        ['id' => 'C-1042', 'major' => '요양보호', 'minor' => '직무향상', 'sub' => '감염관리',
            'title' => '요양보호사 직무향상 1차시 · 감염관리', 'writer' => '김기안',
            'tags' => '감염관리, 위생', 'archive' => '법정의무교육',
            'year' => '2021', 'min' => '24', 'sec' => '10',
            'state' => '공개', 'tone' => 'green', 'at' => '2021.07.31'],
        ['id' => 'C-1041', 'major' => '방문간호', 'minor' => '기록관리', 'sub' => '기록지',
            'title' => '방문간호 기록지 작성 가이드', 'writer' => '이대리',
            'tags' => '기록지, 서식', 'archive' => '실무자료',
            'year' => '2021', 'min' => null, 'sec' => null,
            'state' => '검수중', 'tone' => 'orange', 'at' => '2021.07.30'],
        ['id' => 'C-1040', 'major' => '치매전문', 'minor' => '의사소통', 'sub' => '라포형성',
            'title' => '치매전문교육 2차시 · 의사소통', 'writer' => '박사원',
            'tags' => '치매, 라포', 'archive' => '전문교육',
            'year' => '2020', 'min' => '31', 'sec' => '45',
            'state' => '공개', 'tone' => 'green', 'at' => '2021.07.30'],
        ['id' => 'C-1039', 'major' => '공통', 'minor' => '안전관리', 'sub' => '사고예방',
            'title' => '안전사고 예방 체크리스트', 'writer' => '최주임',
            'tags' => '안전, 점검', 'archive' => '실무자료',
            'year' => '2021', 'min' => null, 'sec' => null,
            'state' => '비공개', 'tone' => 'neutral', 'at' => '2021.07.29'],
        ['id' => 'C-1038', 'major' => '공통', 'minor' => '인권보호', 'sub' => '노인학대',
            'title' => '노인학대 예방 교육 · 사례 중심', 'writer' => '정과장',
            'tags' => '노인학대, 신고의무', 'archive' => '법정의무교육',
            'year' => '2020', 'min' => '18', 'sec' => '02',
            'state' => '반려', 'tone' => 'red', 'at' => '2021.07.28'],
    ];
@endphp

<x-layout title="컨텐츠 관리">
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
            <x-breadcrumb :items="[['label' => '홈', 'href' => '#'], ['label' => '컨텐츠 관리']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">컨텐츠 관리</h1>
        </x-slot:title>

        <x-slot:actions>
            {{-- 셸의 actions 슬롯은 gap-2(8) 라 텍스트 버튼과 채운 버튼이 붙어 보인다.
                 여기서 한 번 더 감싸 간격을 20 으로 벌린다. --}}
            <div class="flex flex-wrap items-center gap-5">
                {{-- 기준일 — 원본 우측 정렬. 라벨 Bold · 날짜 Regular · 14px --}}
                <button type="button"
                        class="inline-flex items-center gap-2 text-label-1 text-mono-black transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                    <span class="font-bold">기준일</span>
                    <span>2021.08.01</span>
                    <x-icon-caret-down class="size-3.5 shrink-0" />
                </button>

                {{-- 컨텐츠 추가 — 원본(node 1-299)에 없다. 목록만 있고 만드는 길이 없어서 넣었다.
                     등록 화면은 /contents/new (레이아웃은 Figma node 1002-269747). --}}
                <x-button variant="primary" size="sm" icon="plus" href="{{ url('/contents/new') }}">컨텐츠 추가</x-button>
            </div>
        </x-slot:actions>

        {{-- ═══ 컨텐츠 목록 ═══ --}}
        {{-- 필터 바 — 청담원 DS Filter Bar(Figma 99:22555) 를 그대로 옮겼다.
             표준은 검색 + 필터 + 「총 N건」을 한 줄로 묶어 표 바로 위에 두는 것이다.
             ⚠️ 정적 화면이라 실제 재조회는 없다. 원본은 감싸는 GET form 을 제출해 목록을
                다시 불러오는데, 여기엔 form 이 없어 submit() 이 no-op 으로 지나간다. --}}
        <x-filter-bar
            search="컨텐츠ID · 제목 검색"
            :count="count($rows)"
            :active="['major', 'archive']"
            :columns="[
                ['key' => 'major', 'label' => '대분류', 'type' => 'select', 'options' => ['요양보호', '방문간호', '치매전문', '공통']],
                ['key' => 'minor', 'label' => '중분류', 'type' => 'select', 'options' => ['직무향상', '기록관리', '의사소통', '안전관리', '인권보호']],
                ['key' => 'sub', 'label' => '소분류', 'type' => 'search', 'options' => ['감염관리', '기록지', '라포형성', '사고예방', '노인학대']],
                ['key' => 'writer', 'label' => '등록자', 'type' => 'search', 'options' => ['김기안', '이대리', '박사원', '최주임', '정과장']],
                ['key' => 'tags', 'label' => '태그명', 'type' => 'search', 'options' => ['감염관리', '위생', '기록지', '서식', '치매', '라포', '안전', '점검', '노인학대', '신고의무']],
                ['key' => 'archive', 'label' => '아카이브 분류', 'type' => 'select', 'options' => ['법정의무교육', '전문교육', '실무자료']],
                ['key' => 'year', 'label' => '제작연도', 'type' => 'select', 'options' => ['2021', '2020', '2019']],
                ['key' => 'state', 'label' => '상태', 'type' => 'select', 'options' => ['공개', '검수중', '비공개', '반려']],
                ['key' => 'at', 'label' => '등록일', 'type' => 'date'],
            ]"
            class="pb-3"
        />

        {{-- 컬럼 13개 — 합계가 넓어서 좁은 화면에서는 가로 스크롤로 넘긴다. --}}
        <x-table selectable min-width="1800px">
            <x-table.head
                selectable
                :all-ids="collect($rows)->pluck('id')->all()"
                :columns="[
                    ['label' => '컨텐츠ID', 'width' => '110px'],
                    ['label' => '대분류', 'width' => '110px'],
                    ['label' => '중분류', 'width' => '110px'],
                    ['label' => '소분류', 'width' => '110px'],
                    ['label' => '제목'],
                    ['label' => '등록자', 'width' => '100px'],
                    ['label' => '태그명', 'width' => '170px'],
                    ['label' => '아카이브 분류', 'width' => '130px'],
                    ['label' => '제작연도', 'align' => 'right', 'width' => '100px'],
                    ['label' => '영상 분', 'align' => 'right', 'width' => '100px'],
                    ['label' => '영상 초', 'align' => 'right', 'width' => '100px'],
                    ['label' => '상태', 'align' => 'center', 'width' => '110px'],
                    ['label' => '등록일', 'align' => 'right', 'width' => '110px'],
                ]"
            />
            <tbody>
                @foreach ($rows as $row)
                    {{-- 행 전체가 상세로 가는 링크다. <tr> 을 <a> 로 감쌀 수 없어서 제목 셀의
                         링크를 행 전체로 늘렸다(after:inset-0). 자바스크립트 없이 동작하고
                         키보드·스크린리더에도 제대로 잡히는 진짜 <a> 다.
                         체크박스는 x-table.row 안에서 z-10 이라 이 면 위에 남는다. --}}
                    <x-table.row selectable :value="$row['id']" class="relative">
                        {{-- ID 는 데이터다. <code> 로 감싸면 Tailwind preflight 가 code 태그에
                             모노스페이스를 물려서 Pretendard 가 아니게 된다. --}}
                        <x-table.cell tone="muted" nowrap>
                            <span class="text-label-2 tabular-nums">{{ $row['id'] }}</span>
                        </x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['major'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['minor'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['sub'] }}</x-table.cell>
                        <x-table.cell tone="strong">
                            <a href="{{ url('/contents/detail') }}"
                               class="after:absolute after:inset-0 focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4">
                                {{ $row['title'] }}
                            </a>
                        </x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['writer'] }}</x-table.cell>
                        <x-table.cell tone="muted">{{ $row['tags'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['archive'] }}</x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>
                            <span class="tabular-nums">{{ $row['year'] }}</span>
                        </x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>
                            <span class="tabular-nums">{{ $unit($row['min'], '분') }}</span>
                        </x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>
                            <span class="tabular-nums">{{ $unit($row['sec'], '초') }}</span>
                        </x-table.cell>
                        <x-table.cell align="center">
                            <x-badge :color="$row['tone']" size="sm">{{ $row['state'] }}</x-badge>
                        </x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>
                            <span class="tabular-nums">{{ $row['at'] }}</span>
                        </x-table.cell>
                    </x-table.row>
                @endforeach
            </tbody>
        </x-table>
    </x-workspace-shell>
</x-layout>
