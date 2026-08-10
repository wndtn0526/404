{{-- 예산 계정 관리 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-93118)
     재무 > 업무 관리자 메뉴의 한 탭. 왼쪽에 비용 분류, 오른쪽에 그 분류에 달린 비용 내역.

     원본 실측(1920) — 본문 1520 (좌 320 · 우 80)
       제목 '업무 관리자 메뉴' 30 Bold lh39 · 알약 탭 줄 (조직 관리 화면과 같은 모양)
       좌 카드 400x823 · 제목 20 Bold lh30 · '+ 추가' 59x30 우측 · 표는 카드 폭 전체
         프로그램 이름 260 | 계정 코드 140 = 400 · 행 56 · 12행
       우 카드 1096x782 · '+ 내역 추가' 78x26 우측
         표 내용 폭 1240 인데 카드가 1096 이라 원본도 잘려 있다 — 가로 스크롤이다
         비용 분류 160 | 계정 이름 180 | 계정 이름 (영어) 200 | 계정 코드 80 | 1인 한도 80
           | 설명 80 | 대상 인원 80 | 사용자 가이드 180 | 사용자 가이드 (영어) 200 = 1240
         행 56 · 10행 · 아래 페이지네이션 + '10개씩 보기' 92x26
       카드 사이 24 (400 + 24 + 1096 = 1520)

     ⚠️ 원본 탭 줄에 '인사 정보 조회' 가 두 번 들어가 있다. 재무 메뉴에 인사 탭이 있는 것도
        그렇고 자리표시자로 보여서 하나만 뒀다.
     ⚠️ 예산 계정 관리 말고는 화면이 없다. 나머지 탭은 링크 없이 글자로만 둔다.
     ⚠️ 원본은 계정 이름과 영어 이름이 서로 어긋나 있다(미팅비 → IT computing equipment 등).
        자리표시자를 돌려 쓴 것으로 보여 여기서는 한국어에 맞는 영어로 적었다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다.
     ⚠️ '+ 추가' · '+ 내역 추가' 는 아직 아무 일도 하지 않는다. 붙일 때는 모달로 받고
        POST + CSRF 로 보낸다. --}}
@php
    // 재무 > 업무 관리자 메뉴 탭. 예산 계정 관리만 화면이 있다.
    $adminTabs = [
        'budget' => ['label' => '예산 계정 관리', 'href' => url('/finance/budget')],
        'hr' => ['label' => '인사 정보 조회'],
        'stamp' => ['label' => '직인 및 워터마크 관리'],
        'cert' => ['label' => '증명서 신청 관리'],
        'change' => ['label' => '정보 변경 요청 관리'],
        'attendance' => ['label' => '근태 기준 관리'],
    ];

    // 좌 — 비용 분류. 계정 코드는 아직 안 붙였다(원본도 전부 하이픈이다).
    $groups = [
        ['name' => '교육 훈련비', 'code' => null],
        ['name' => '교통비', 'code' => null],
        ['name' => '기타 수수료', 'code' => null],
        ['name' => '마케팅비', 'code' => null],
        ['name' => '복리 후생비', 'code' => null],
        ['name' => '비품 · 소프트웨어', 'code' => null],
        ['name' => '소모품비', 'code' => null],
        ['name' => '식대', 'code' => null],
        ['name' => '조직관리비', 'code' => null],
        ['name' => '퀵 · 택배 등', 'code' => null],
        ['name' => '회사 공용 카드 정산', 'code' => null],
        ['name' => '회사 개인 카드 정산', 'code' => null],
    ];

    // 우 — 비용 내역. 사용자 가이드는 계정 이름과 같은 값이 들어간다(원본 그대로).
    $rows = [
        ['group' => '교육 훈련비', 'name' => 'IT 전산 장비 (PC 등)', 'name_en' => 'IT computing equipment', 'code' => '21001'],
        ['group' => '교통비', 'name' => '거래처 접대비', 'name_en' => 'Client entertainment', 'code' => '13003'],
        ['group' => '기타 수수료', 'name' => '기타 지급 수수료', 'name_en' => 'Other payment fees', 'code' => '21001'],
        ['group' => '마케팅비', 'name' => '도서구매 및 인쇄물', 'name_en' => 'Book purchase and printing', 'code' => '13003'],
        ['group' => '복리 후생비', 'name' => '미팅비', 'name_en' => 'Meeting expenses', 'code' => '21001'],
        ['group' => '비품 · 소프트웨어', 'name' => '소프트웨어 (기간 사용)', 'name_en' => 'Software subscription', 'code' => '13003'],
        ['group' => '소모품비', 'name' => '소모품비', 'name_en' => 'Consumables', 'code' => '21001'],
        ['group' => '식대', 'name' => '식대', 'name_en' => 'Meal expenses', 'code' => '13003'],
        ['group' => '조직관리비', 'name' => '조직 관리비', 'name_en' => 'Organization management', 'code' => '21001'],
        ['group' => '퀵 · 택배 등', 'name' => '통신비', 'name_en' => 'Communication expenses', 'code' => '13003'],
    ];

    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
@endphp

<x-layout title="예산 계정 관리">
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
            @include('partials.workspace-tabs', ['active' => 'budget', 'tabs' => $adminTabs])
        </x-slot:title>

        <div class="mt-8 flex min-w-0 flex-col gap-6 pb-10 xl:flex-row xl:items-start">

            {{-- ═══ 좌: 비용 분류 400 ═══ --}}
            <section class="w-full min-w-0 shrink-0 rounded-lg bg-background-normal pb-[30px] xl:w-[400px]">
                <div class="flex min-w-0 items-center justify-between gap-3 px-[30px] pt-[30px]">
                    <h2 class="{{ $cardTitle }}">비용 분류</h2>
                    {{-- 원본 59x30 — DS 버튼 sm 은 40 이라 한 단계 크다 --}}
                    <x-button variant="outline" size="sm" icon="plus">추가</x-button>
                </div>

                {{-- 표는 카드 폭 전체로 흘린다(원본 그대로) --}}
                <div class="pt-5">
                    <x-table min-width="400px" class="rounded-none border-x-0">
                        <x-table.head :columns="[
                            ['label' => '프로그램 이름', 'width' => '260px'],
                            ['label' => '계정 코드'],
                        ]" />
                        <tbody>
                            @forelse ($groups as $group)
                                <x-table.row>
                                    <x-table.cell tone="strong">{{ $group['name'] }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>{{ $group['code'] ?? '-' }}</x-table.cell>
                                </x-table.row>
                            @empty
                                <x-table.empty :colspan="2">비용 분류가 없습니다.</x-table.empty>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>
            </section>

            {{-- ═══ 우: 비용 내역 ═══ --}}
            <section class="min-w-0 flex-1 rounded-lg bg-background-normal pb-[30px]">
                <div class="flex min-w-0 items-center justify-between gap-3 px-[30px] pt-[30px]">
                    <h2 class="{{ $cardTitle }}">비용 내역</h2>
                    <x-button variant="outline" size="sm" icon="plus">내역 추가</x-button>
                </div>

                {{-- 열 합이 1240 이라 카드보다 넓다. 원본도 잘려 있고 가로로 넘긴다. --}}
                <div class="pt-5">
                    <x-table min-width="1240px" class="rounded-none border-x-0">
                        <x-table.head :columns="[
                            ['label' => '비용 분류', 'width' => '160px'],
                            ['label' => '계정 이름', 'width' => '180px'],
                            ['label' => '계정 이름 (영어)', 'width' => '200px'],
                            ['label' => '계정 코드', 'width' => '80px'],
                            ['label' => '1인 한도', 'width' => '80px'],
                            ['label' => '설명', 'width' => '80px'],
                            ['label' => '대상 인원', 'width' => '80px'],
                            ['label' => '사용자 가이드', 'width' => '180px'],
                            ['label' => '사용자 가이드 (영어)'],
                        ]" />
                        <tbody>
                            @forelse ($rows as $row)
                                <x-table.row>
                                    <x-table.cell tone="muted" nowrap>{{ $row['group'] }}</x-table.cell>
                                    <x-table.cell tone="strong">{{ $row['name'] }}</x-table.cell>
                                    <x-table.cell tone="muted">{{ $row['name_en'] }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>
                                        <span class="tabular-nums">{{ $row['code'] }}</span>
                                    </x-table.cell>
                                    {{-- 한도·설명·대상 인원은 아직 값이 없다(원본도 전부 같은 문구다) --}}
                                    <x-table.cell tone="muted" nowrap>한도 없음</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>내용 없음</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>내용 없음</x-table.cell>
                                    <x-table.cell tone="muted">{{ $row['name'] }}</x-table.cell>
                                    <x-table.cell tone="muted">{{ $row['name_en'] }}</x-table.cell>
                                </x-table.row>
                            @empty
                                <x-table.empty :colspan="9">비용 내역이 없습니다.</x-table.empty>
                            @endforelse
                        </tbody>
                    </x-table>
                </div>

                {{-- 원본은 페이지 다섯 개 + '10개씩 보기' 다 --}}
                <div class="px-[30px] pt-[30px]">
                    <x-pagination :total="50" :per-page="10" :current="1" :per-page-options="[10, 50, 100]" />
                </div>
            </section>
        </div>
    </x-workspace-shell>
</x-layout>
