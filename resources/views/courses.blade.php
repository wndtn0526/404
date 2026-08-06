{{-- 과정 관리 — 컨텐츠 관리에 올라간 컨텐츠를 묶어 만든 '과정' 목록.
     크롬(LNB·GNB·헤더)은 <x-workspace-shell> 이 갖고 있다.

     ⚠️ 이 화면은 Figma 에 디자인이 없다. 컨텐츠 관리(node 1-299)와 같은 뼈대로 짰다 —
        같은 알약 탭 줄, 같은 필터 바, 같은 DS 표. 디자인이 나오면 컬럼·정렬을 맞춰야 한다.

     컬럼은 컨텐츠 관리 표에서 따왔다. 과정 고유는 두 개다:
        차시        — 묶인 컨텐츠 수
        총 재생시간  — 묶인 컨텐츠의 영상 길이 합
     둘 다 사람이 입력하는 값이 아니라 묶은 결과다. 그래서 추가 화면에서도 손입력이 아니라
     고른 컨텐츠에서 계산해 보여준다.

     ⚠️ 행 데이터는 뷰에 박아둔 예시다. DB 에서 오지 않는다. 모델·서비스가 붙으면
        컨트롤러에서 넘겨받는다(로직은 Service Layer).
     ⚠️ 환급/비환급 같은 교육 유형 구분은 넣지 않았다. 규정(고용노동부·산업인력공단)이
        걸리는 값이라 담당자 확인 없이 화면에 세우지 않는다. --}}
@php
    // 총 재생시간 표기 — 값이 없으면 하이픈. em dash 는 쓰지 않는다.
    $runtime = function (?int $sec): string {
        if ($sec === null || $sec <= 0) {
            return '-';
        }

        return str_pad((string) intdiv($sec, 60), 2, '0', STR_PAD_LEFT).'분 '
            .str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT).'초';
    };

    $rows = [
        ['id' => 'CO-2104', 'major' => '요양보호', 'minor' => '직무향상',
            'title' => '요양보호사 직무향상 과정 (2021)', 'archive' => '법정의무교육',
            'lessons' => 3, 'sec' => 4530, 'writer' => '김기안',
            'state' => '공개', 'tone' => 'green', 'at' => '2021.07.31'],
        ['id' => 'CO-2103', 'major' => '치매전문', 'minor' => '의사소통',
            'title' => '치매전문교육 심화 과정', 'archive' => '전문교육',
            'lessons' => 2, 'sec' => 2987, 'writer' => '박사원',
            'state' => '공개', 'tone' => 'green', 'at' => '2021.07.30'],
        ['id' => 'CO-2102', 'major' => '공통', 'minor' => '인권보호',
            'title' => '노인학대 예방 법정의무교육', 'archive' => '법정의무교육',
            'lessons' => 1, 'sec' => 1082, 'writer' => '정과장',
            'state' => '검수중', 'tone' => 'orange', 'at' => '2021.07.29'],
        ['id' => 'CO-2101', 'major' => '방문간호', 'minor' => '기록관리',
            'title' => '방문간호 실무자 기록 과정', 'archive' => '실무자료',
            'lessons' => 2, 'sec' => null, 'writer' => '이대리',
            'state' => '비공개', 'tone' => 'neutral', 'at' => '2021.07.28'],
    ];
@endphp

<x-layout title="과정 관리">
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
            <x-breadcrumb :items="[['label' => '홈', 'href' => url('/workspace')], ['label' => '과정 관리']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">과정 관리</h1>
            @include('partials.workspace-tabs', ['active' => 'courses'])
        </x-slot:title>

        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-5">
                <button type="button"
                        class="inline-flex items-center gap-2 text-label-1 text-mono-black transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                    <span class="font-bold">기준일</span>
                    <span>2021.08.01</span>
                    <x-icon-caret-down class="size-3.5 shrink-0" />
                </button>

                <x-button variant="primary" size="sm" icon="plus" href="{{ url('/courses/new') }}">과정 추가</x-button>
            </div>
        </x-slot:actions>

        <x-filter-bar
            search="과정ID · 과정명 검색"
            :count="count($rows)"
            :active="['major', 'archive']"
            :columns="[
                ['key' => 'major', 'label' => '대분류', 'type' => 'select', 'options' => ['요양보호', '방문간호', '치매전문', '공통']],
                ['key' => 'minor', 'label' => '중분류', 'type' => 'select', 'options' => ['직무향상', '기록관리', '의사소통', '안전관리', '인권보호']],
                ['key' => 'archive', 'label' => '아카이브 분류', 'type' => 'select', 'options' => ['법정의무교육', '전문교육', '실무자료']],
                ['key' => 'writer', 'label' => '등록자', 'type' => 'search', 'options' => ['김기안', '이대리', '박사원', '최주임', '정과장']],
                ['key' => 'state', 'label' => '상태', 'type' => 'select', 'options' => ['공개', '검수중', '비공개', '반려']],
                ['key' => 'at', 'label' => '등록일', 'type' => 'date'],
            ]"
            class="pb-3"
        />

        <x-table selectable min-width="1440px">
            <x-table.head
                selectable
                :all-ids="collect($rows)->pluck('id')->all()"
                :columns="[
                    ['label' => '과정ID', 'width' => '120px'],
                    ['label' => '대분류', 'width' => '110px'],
                    ['label' => '중분류', 'width' => '110px'],
                    ['label' => '과정명'],
                    ['label' => '아카이브 분류', 'width' => '130px'],
                    ['label' => '차시', 'align' => 'right', 'width' => '90px'],
                    ['label' => '총 재생시간', 'align' => 'right', 'width' => '130px'],
                    ['label' => '등록자', 'width' => '100px'],
                    ['label' => '상태', 'align' => 'center', 'width' => '110px'],
                    ['label' => '등록일', 'align' => 'right', 'width' => '110px'],
                ]"
            />
            <tbody>
                @foreach ($rows as $row)
                    <x-table.row selectable :value="$row['id']">
                        <x-table.cell tone="muted" nowrap>
                            <span class="text-label-2 tabular-nums">{{ $row['id'] }}</span>
                        </x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['major'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['minor'] }}</x-table.cell>
                        <x-table.cell tone="strong">{{ $row['title'] }}</x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['archive'] }}</x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>
                            <span class="tabular-nums">{{ $row['lessons'] }}차시</span>
                        </x-table.cell>
                        <x-table.cell align="right" tone="muted" nowrap>
                            <span class="tabular-nums">{{ $runtime($row['sec']) }}</span>
                        </x-table.cell>
                        <x-table.cell tone="muted" nowrap>{{ $row['writer'] }}</x-table.cell>
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
