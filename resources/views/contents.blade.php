{{-- 컨텐츠 관리 — Figma 워크스페이스 화면 (Lnsej46BaxtyKq3rhssFH3 · node 1-299)
     크롬(LNB·GNB·헤더)은 <x-workspace-shell> 이 갖고 있다. 이 파일은 화면 고유의
     브레드크럼·타이틀·탭·기준일과 본문 표를 채운다.

     ⚠️ 표는 Figma 에 디자인이 없다. 원본 node 1-299 의 본문은 비어 있다.
        DS 표 컴포넌트(Data Tables 1002:523369)로 조립하고 컬럼은 컨텐츠 관리에
        필요할 만한 것으로 잡았다. 디자인이 나오면 컬럼·정렬·상태값을 맞춰야 한다.

     ⚠️ 행 데이터는 뷰에 박아둔 예시다. DB 에서 오지 않는다. 모델·서비스가 붙으면
        컨트롤러에서 넘겨받는다(로직은 Service Layer). --}}
@php
    // 상태 배지 색은 완성된 토큰 이름을 담는다 — x-badge 의 color 매트릭스가 받는다.
    $rows = [
        ['id' => 'C-1042', 'title' => '요양보호사 직무향상 1차시 — 감염관리', 'type' => '동영상',
            'group' => '요양보호', 'writer' => '김기안', 'runtime' => '24:10',
            'state' => '공개', 'tone' => 'green', 'at' => '2021.07.31'],
        ['id' => 'C-1041', 'title' => '방문간호 기록지 작성 가이드', 'type' => '문서',
            'group' => '방문간호', 'writer' => '이대리', 'runtime' => '—',
            'state' => '검수중', 'tone' => 'orange', 'at' => '2021.07.30'],
        ['id' => 'C-1040', 'title' => '치매전문교육 2차시 — 의사소통', 'type' => '동영상',
            'group' => '치매전문', 'writer' => '박사원', 'runtime' => '31:45',
            'state' => '공개', 'tone' => 'green', 'at' => '2021.07.30'],
        ['id' => 'C-1039', 'title' => '안전사고 예방 체크리스트', 'type' => '문서',
            'group' => '공통', 'writer' => '최주임', 'runtime' => '—',
            'state' => '비공개', 'tone' => 'neutral', 'at' => '2021.07.29'],
        ['id' => 'C-1038', 'title' => '노인학대 예방 교육 — 사례 중심', 'type' => '동영상',
            'group' => '공통', 'writer' => '정과장', 'runtime' => '18:02',
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

            {{-- 워크스페이스 탭 — 원본 Box34. 활성은 검정 채움 + 닫기, 비활성은 Warm gray/200.
                 DS <x-tabs> 는 밑줄형이라 형태가 다르다. 원본이 알약형이어서 여기서 조립했다. --}}
            <div class="flex items-start gap-4">
                <span class="inline-flex items-center justify-center gap-1 rounded-lg bg-mono-black pb-[5px] pl-3 pr-2.5 pt-1.5">
                    <span class="text-body-2 font-bold leading-[23px] text-white">컨텐츠 관리</span>
                    <button type="button"
                            class="inline-flex shrink-0 items-center pb-px text-white transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                            aria-label="컨텐츠 관리 탭 닫기">
                        <x-icon-close class="size-[18px]" />
                    </button>
                </span>

                <a href="#"
                   class="inline-flex items-center justify-center rounded-lg bg-warm-gray-200 px-3 pb-[5px] pt-1.5 text-body-2 font-bold leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">
                    과정 관리
                </a>
            </div>
        </x-slot:title>

        <x-slot:actions>
            {{-- 기준일 — 원본 우측 정렬. 라벨 Bold · 날짜 Regular · 14px --}}
            <button type="button"
                    class="inline-flex items-center gap-2 text-label-1 text-mono-black transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                <span class="font-bold">기준일</span>
                <span>2021.08.01</span>
                <x-icon-caret-down class="size-3.5 shrink-0" />
            </button>
        </x-slot:actions>

        {{-- ═══ 컨텐츠 목록 ═══ --}}
        <div class="flex items-baseline justify-between pb-3">
            <p class="text-label-1 text-label-alternative">
                전체 <strong class="font-semibold text-label-normal">{{ count($rows) }}</strong>건
            </p>
        </div>

        <x-table selectable min-width="1080px">
            <x-table.head
                selectable
                :all-ids="collect($rows)->pluck('id')->all()"
                :columns="[
                    ['label' => '컨텐츠 ID', 'width' => '120px'],
                    ['label' => '제목'],
                    ['label' => '유형', 'align' => 'center', 'width' => '90px'],
                    ['label' => '분류', 'width' => '110px'],
                    ['label' => '등록자', 'width' => '100px'],
                    ['label' => '재생시간', 'align' => 'right', 'width' => '100px'],
                    ['label' => '상태', 'align' => 'center', 'width' => '110px'],
                    ['label' => '등록일', 'align' => 'right', 'width' => '110px'],
                ]"
            />
            <tbody>
                @foreach ($rows as $row)
                    <x-table.row selectable :value="$row['id']">
                        {{-- ID 는 데이터다. <code> 로 감싸면 Tailwind preflight 가 code 태그에
                             모노스페이스를 물려서 Pretendard 가 아니게 된다. --}}
                        <x-table.cell tone="muted" nowrap>
                            <span class="text-label-2 tabular-nums">{{ $row['id'] }}</span>
                        </x-table.cell>
                        <x-table.cell tone="strong">{{ $row['title'] }}</x-table.cell>
                        <x-table.cell align="center" tone="muted" nowrap>{{ $row['type'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['group'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['writer'] }}</x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>{{ $row['runtime'] }}</x-table.cell>
                        <x-table.cell align="center">
                            <x-badge :color="$row['tone']" size="sm">{{ $row['state'] }}</x-badge>
                        </x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>{{ $row['at'] }}</x-table.cell>
                    </x-table.row>
                @endforeach
            </tbody>
        </x-table>
    </x-workspace-shell>
</x-layout>
