{{-- 지출 결의서 정산 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-92909)
     재무 > 업무 관리자 메뉴의 한 탭. 승인이 끝난 거래처 지출 결의서를 정산 대기 목록으로 본다.
     개인 비용 정산(1002-92654)과 표 앞부분이 같고 뒤 세 열만 다르다.

     원본 실측(1920) — 본문 1520
       검색·필터 카드 1520x212 · 조회 목록 카드 1520x782 (사이 16)
       조회 목록 제목 20 Bold + 「총 N건」 · 우측 버튼 셋
       표 열 80 | 80 | 100 | 350 | 350 | 140 | 120 | 300 = 1520 · 행 56 · 10행
       아래 페이지네이션 + '10개씩 보기'

     ⚠️ 원본 필터는 검색 한 줄 + 드롭다운 여섯 + '조회' 버튼짜리 카드다. 컨텐츠 관리부터
        쓰기로 한 공용 x-filter-bar 로 바꿨다 — 화면마다 필터 생김새가 갈리면 안 된다.
        '조회' 버튼도 없앴다. 필터 바는 칩에서 '적용'을 누르면 다시 불러온다.
     ⚠️ 조회 목록 커스텀 · 엑셀로 저장 · 선택 항목 일괄 처리는 아직 동작하지 않는다.
        일괄 처리는 상태를 바꾸는 일이라 붙일 때 POST + CSRF + Policy 로 간다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다. --}}
@php
    $rows = [];
    for ($i = 1; $i <= 10; $i++) {
        $rows[] = [
            'state' => $i % 2 === 1 ? '승인' : '대기',
            'approved_at' => '2021.12.'.str_pad((string) (30 - $i), 2, '0', STR_PAD_LEFT),
            'no' => 'CDW-210609-'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
            'doc' => '지출 결의서 (거래처)',
            'owner' => '청담원',
            'paid_at' => '2021.12.'.str_pad((string) (30 - $i), 2, '0', STR_PAD_LEFT),
            'vendor' => ['워크 앤 조이', '한빛 인쇄', '누리 스튜디오'][$i % 3],
        ];
    }
@endphp

<x-layout title="지출 결의서 정산">
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
            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '재무', 'href' => url('/finance')],
                ['label' => '업무 관리자 메뉴'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">업무 관리자 메뉴</h1>
        </x-slot:title>

        <x-filter-bar
            search="업무 번호 · 업체 이름 검색"
            :active="['used', 'state']"
            :columns="[
                ['key' => 'used', 'label' => '사용일자', 'type' => 'date'],
                ['key' => 'project', 'label' => '프로젝트 이름', 'type' => 'search', 'options' => ['요양보호사 개편', '방문간호 신규', '치매전문 보수']],
                ['key' => 'writer', 'label' => '신청자', 'type' => 'search', 'options' => ['김기안', '이대리', '박사원', '최주임', '정과장']],
                ['key' => 'approved', 'label' => '승인 완료일', 'type' => 'date'],
                ['key' => 'state', 'label' => '정산 상태', 'type' => 'select', 'options' => ['승인', '대기', '반려']],
            ]"
            class="mt-8 pb-3"
        />

        <section class="min-w-0 rounded-lg bg-background-normal pb-[30px]">
            <div class="flex min-w-0 flex-wrap items-center justify-between gap-3 px-[30px] pt-[30px]">
                <div class="flex min-w-0 flex-wrap items-baseline gap-4">
                    <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">조회 목록</h2>
                    <span class="text-label-1 font-medium leading-5 text-label-alternative">총 {{ number_format(count($rows) * 100) }} 건</span>
                </div>

                {{-- 원본 버튼 셋. '선택 항목 일괄 처리' 는 원본이 비활성으로 그려져 있다 —
                     고른 행이 없어서다. 표의 선택 상태는 x-table 안에 있어서 여기서 못 본다.
                     실제로 붙일 때는 선택 상태를 위로 올려 잠금을 풀어 준다. --}}
                <div class="flex flex-wrap items-center gap-2">
                    <x-button variant="outline" size="sm" icon="setting">조회 목록 커스텀</x-button>
                    <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                    <x-button variant="primary" size="sm" disabled>선택 항목 일괄 처리</x-button>
                </div>
            </div>

            {{-- 표는 카드 폭 전체로 흘린다(원본 그대로) --}}
            <div class="pt-5">
                <x-table selectable min-width="1520px" class="rounded-none border-x-0">
                    <x-table.head
                        selectable
                        :all-ids="collect($rows)->pluck('no')->all()"
                        :columns="[
                            ['label' => '상태', 'width' => '80px'],
                            ['label' => '승인 완료일', 'width' => '100px'],
                            ['label' => '업무 번호', 'width' => '350px'],
                            ['label' => '문서 이름', 'width' => '350px'],
                            ['label' => '귀속처', 'width' => '140px'],
                            ['label' => '결제 날짜', 'width' => '120px'],
                            ['label' => '업체 이름'],
                        ]"
                    />
                    <tbody>
                        @forelse ($rows as $row)
                            <x-table.row selectable :value="$row['no']">
                                <x-table.cell nowrap>
                                    <x-badge :color="$row['state'] === '승인' ? 'green' : 'neutral'" size="sm">{{ $row['state'] }}</x-badge>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>
                                    <span class="tabular-nums">{{ $row['approved_at'] }}</span>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>
                                    <span class="tabular-nums">{{ $row['no'] }}</span>
                                </x-table.cell>
                                <x-table.cell tone="strong">{{ $row['doc'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $row['owner'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>
                                    <span class="tabular-nums">{{ $row['paid_at'] }}</span>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $row['vendor'] }}</x-table.cell>
                            </x-table.row>
                        @empty
                            <x-table.empty :colspan="8">정산할 지출 결의서가 없습니다.</x-table.empty>
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="px-[30px] pt-[30px]">
                <x-pagination :total="1000" :per-page="10" :current="1" :per-page-options="[10, 50, 100]" />
            </div>
        </section>
    </x-workspace-shell>
</x-layout>
