{{-- 인사 관리 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-260139)
     멤버의 인사 정보를 한 표로 본다. 인사 메뉴의 첫 화면이다.

     원본 실측(1920) — 본문 1520 (x320~1840)
       제목 24 Bold lh39 · 우상단 '기준일 2021.08.01'
       검색·필터 카드 1520x156 · 안쪽 30 — 검색 1260x40 + 필터 40 + '조회' 128x40
         아랫줄 드롭다운 넷 294 + 초기화
       조회 결과 목록 카드 1520x782 · 안쪽 30
         제목 20 Bold + '총 1,000 건' · 우측 '엑셀로 저장' 93x30
         표 15열 · 머리 56 · 줄 56 · 열 열 개 · 아래 작은 페이지네이션

     ⚠️ 원본 검색·필터는 이 화면 전용 카드다. 컨텐츠 관리부터 쓰기로 한 공용 x-filter-bar 로
        바꿨다 — 화면마다 필터 생김새가 갈리면 안 된다. '조회' 버튼도 없앴다(입력하면 걸린다).
     ⚠️ 원본은 제목 옆에 칩 탭이 일곱 개 붙는다(인사 정보 조회 · 인사 자료 관리 · 직인 및
        워터마크 관리 · 증명서 신청 관리 · 교육 관리 · 정보 변경 요청 관리 · 근태 기준 관리).
        그 여섯은 화면이 없어 눌러도 표가 바뀌지 않아 통째로 뺐다. 화면이 생기면 그때 되살린다.
     ⚠️ '기준일' 은 이 표를 어느 시점 기준으로 볼지 고르는 값이다. 원본이 캐럿 달린 글자라
        DS 드롭다운 대신 그 모양을 따랐다 — 붙일 때는 날짜 선택기가 필요하다.
     ⚠️ '엑셀로 저장' 은 아직 갈 곳이 없다. 붙일 때는 권한을 Policy 에서 보고, 내려받는 값에
        개인정보가 들어가므로 담당자 확인부터 한다(CLAUDE.md).
     ⚠️ 연락처는 개인정보다. 예시값도 실제 형식으로 두지 않았다 — 자리수만 맞춘 0 이다.
        실제로 붙을 때 평문 저장·평문 로그를 하지 않는다. 화면에도 가리는 게 맞는지
        담당자 확인이 필요하다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다. --}}
@php
    // 표 예시 — 원본 열 순서 그대로다.
    $roles = [
        ['디자인팀', '디자이너'], ['기획팀', '기획자'], ['개발팀', '개발자'],
        ['운영팀', '매니저'], ['콘텐츠팀', '에디터'],
    ];
    $names = ['심프로', '정프로', '오프로', '최프로', '장프로', '곽프로', '황프로', '문프로', '유프로', '한프로'];

    $members = [];
    foreach ($names as $i => $name) {
        [$team, $role] = $roles[$i % count($roles)];
        $members[] = [
            'no' => $i + 1,
            'name' => $name,
            'team' => $team,
            'role' => $role,
            'en' => 'GPRO',
            'account' => 'GPRO@groupware.pro',
            // ⚠️ 실제 형식의 번호를 저장소에 남기지 않는다. 자리수만 맞춘 0 이다.
            'phone' => '000-0000-0000',
            'employment' => $i % 3 === 1 ? '비정규직' : '정규직',
            'entry' => $i % 4 === 0 ? '신입' : '경력',
            'years' => ($i % 10 + 1).'년',
            'joined' => '2021.12.30',
            'contract' => '2021.12.30',
            'contract_end' => '2021.12.30',
            'probation' => '2021.12.30',
            'emp_no' => 'GPR2001'.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
        ];
    }
@endphp

<x-layout title="인사 관리">
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
                ['label' => '인사'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">인사 관리</h1>
        </x-slot:title>

        {{-- 기준일 — 이 표를 어느 시점으로 볼지 고른다.
             셸의 머리 줄은 제목과 actions 를 양 끝으로 벌리므로 여기 두면 오른쪽 끝에 붙는다. --}}
        <x-slot:actions>
            <button type="button"
                    class="inline-flex shrink-0 items-center gap-2 rounded-md p-1 text-label-1 leading-5 text-mono-black transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                <span class="font-medium">기준일</span>
                <span class="tabular-nums">2021.08.01</span>
                <x-icon-chevron-down class="size-3.5 text-label-alternative" />
            </button>
        </x-slot:actions>

        <x-filter-bar
            search="멤버의 이름, 사번, 소속을 검색해보세요"
            :active="['status', 'position', 'employment', 'joined']"
            :columns="[
                ['key' => 'status', 'label' => '재직 상태', 'type' => 'select', 'options' => ['재직 + 휴직', '재직', '휴직', '퇴직']],
                ['key' => 'position', 'label' => '직책', 'type' => 'select', 'options' => ['전체', '팀장', '파트장', '팀원']],
                ['key' => 'employment', 'label' => '고용 형태', 'type' => 'select', 'options' => ['전체', '정규직', '비정규직', '계약직']],
                ['key' => 'joined', 'label' => '입사일', 'type' => 'date'],
            ]"
            class="mt-8"
        />

        {{-- 조회 결과 목록 --}}
        <section class="mt-6 min-w-0 rounded-lg bg-background-normal p-[30px] pb-6">
            <div class="flex min-w-0 flex-wrap items-center gap-3">
                <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">조회 결과 목록</h2>
                <p class="text-label-1 leading-5 text-label-alternative">
                    총 <span class="tabular-nums">1,000</span> 건
                </p>

                {{-- 원본 93x30 — DS 버튼 sm 은 40 이라 한 단계 크다 --}}
                <x-button variant="outline" size="sm" icon="download" class="ml-auto shrink-0">엑셀로 저장</x-button>
            </div>

            {{-- 열이 많아 카드보다 넓다. 카드 안쪽 폭을 꽉 채우고 가로로 넘긴다. --}}
            <div class="-mx-[30px] mt-5">
                <x-table flush min-width="1520px"
                         class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                    <x-table.head :columns="[
                        ['label' => '', 'width' => '60px'],
                        ['label' => '이름', 'width' => '120px'],
                        ['label' => '소속', 'width' => '90px'],
                        ['label' => '역할', 'width' => '90px'],
                        ['label' => '영어 이름', 'width' => '110px'],
                        ['label' => '그룹 웨어 계정', 'width' => '172px'],
                        ['label' => '연락처', 'width' => '116px'],
                        ['label' => '고용 형태', 'width' => '90px'],
                        ['label' => '입사 구분', 'width' => '80px'],
                        ['label' => '근속년수', 'width' => '94px'],
                        ['label' => '입사일', 'width' => '100px'],
                        ['label' => '계약일', 'width' => '100px'],
                        ['label' => '계약 종료일', 'width' => '100px'],
                        ['label' => '수습 만료', 'width' => '100px'],
                        ['label' => '사번', 'width' => '100px'],
                    ]" />
                    <tbody>
                        @foreach ($members as $m)
                            <x-table.row>
                                <x-table.cell tone="muted" nowrap>{{ $m['no'] }}</x-table.cell>
                                <x-table.cell nowrap>
                                    <span class="flex min-w-0 items-center gap-2.5">
                                        <x-thumbnail :name="$m['name']" size="sm" shape="circle" class="shrink-0" />
                                        <span class="min-w-0 truncate font-bold text-label-strong">{{ $m['name'] }}</span>
                                    </span>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $m['team'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $m['role'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $m['en'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $m['account'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['phone'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $m['employment'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $m['entry'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['years'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['joined'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['contract'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['contract_end'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['probation'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $m['emp_no'] }}</x-table.cell>
                            </x-table.row>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <div class="mt-6">
                <x-pagination :total="1000" :per-page="10" :current="1" />
            </div>
        </section>
    </x-workspace-shell>
</x-layout>
