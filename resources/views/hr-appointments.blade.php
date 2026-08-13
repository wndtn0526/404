{{-- 발령 관리 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-262024)
     발령의 '기준' 을 만드는 화면이다. 실제 발령을 내는 곳이 아니라, 어떤 발령 유형이
     무엇을 바꾸는지(조직·직책·근무지 …)를 정해 두는 곳이다.

     원본 실측(1920) — 본문 1520 (x320~1840)
       제목 24 Bold lh39 · 우상단 '기준일 2021.08.01'
       발령 기준 관리 카드 1520x726 · 안쪽 30
         제목 20 Bold + '총 50 건' · 우측 '엑셀로 저장' 93x30 + '발령 기준 추가' 106x30
         표 17열(체크 + 16) · 머리 56 · 줄 56 · 열 줄
       수정일 관리 카드 1520x558 — 표 3열(체크 + 일자 구분 160 + 비고 1280) · 일곱 줄

     ⚠️ 원본은 제목 옆에 칩 탭이 일곱 개 붙는다. 인사 관리에서 빼기로 한 것과 같은 이유로
        여기서도 뺐다 — 그 탭들의 화면이 아직 없어 눌러도 아무 일이 없다.
     ⚠️ 원본 LNB 는 발령 관리 밑으로 한 단이 더 들어간다(발령 기준 관리 · 사원 등록 ·
        발령 등록 · 발령 조회 · 발령 정정 · 겸직 현황 · 파견 현황 · 휴직 현황).
        셸이 두 단까지라 발령 관리만 인사 밑에 뒀다. 화면이 생기면 그때 단을 늘린다.
     ⚠️ 표의 가운데 열 열 개는 켜고 끄는 값이라 체크박스로 나온다. 원본은 읽기 전용 표시라
        누를 수 없게 뒀다 — 고치는 건 '발령 기준 추가' 쪽 일이다.
     ⚠️ '엑셀로 저장'·'추가' 는 아직 갈 곳이 없다. 붙일 때는 POST + CSRF 로 보내고 권한은
        화면이 아니라 Policy 에서 본다.
     ⚠️ 값은 전부 예시다. 체크 상태는 원본 화면에서 읽어 그대로 옮겼다. --}}
@php
    /*
     * 발령 기준 — [유형, 이름, 재직 상태, 시작일, 종료일, 켜진 항목..., 종료 발령]
     * 켜진 항목 차례: 고용 구분 · 조직 · 직책 · 근무지 · 겸직 · 파견 · 퇴직 ·
     *                수습 종료일 · 종료 예정일 · 요약 여부
     */
    $flagLabels = ['고용 구분', '조직', '직책', '근무지', '겸직', '파견', '퇴직', '수습 종료일', '종료 예정일', '요약 여부'];

    $rules = [
        ['채용', '신입 입사', '재직', [1, 1, 1, 1, 0, 0, 0, 1, 0, 1]],
        ['이동', '부서 배치', '재직', [0, 1, 1, 0, 0, 0, 0, 0, 0, 1]],
        ['보직', '대기', '재직', [0, 0, 1, 0, 0, 0, 0, 0, 0, 1]],
        ['휴복직', '질병 휴직', '휴직', [0, 0, 0, 0, 0, 0, 0, 0, 0, 1]],
        ['파견', '국내 파견', '재직', [0, 0, 0, 0, 0, 1, 0, 0, 0, 1]],
        ['직종 전환', '계약직 전환', '재직', [1, 0, 0, 0, 0, 0, 0, 0, 0, 0]],
        ['퇴직', '명예 퇴직', '퇴직', [0, 0, 0, 0, 0, 0, 1, 0, 0, 0]],
        ['기타', '경영진 선임', '재직', [0, 0, 0, 1, 0, 0, 0, 0, 0, 0]],
        ['기타', '수습 해제', '재직', [0, 0, 0, 1, 0, 0, 0, 0, 0, 0]],
        ['기타', '입사 취소', '퇴직', [0, 0, 0, 0, 0, 0, 1, 0, 0, 1]],
    ];

    // 수정일 관리 — 발령이 났을 때 어떤 날짜를 다시 쓰는지
    $dateRules = ['입사일', '그룹 입사일', '근태 기준일', '퇴직 기준일', '최종 이동일', '최종 보임일', '직무 변경일'];

    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
    $card = 'min-w-0 rounded-lg bg-background-normal p-[30px] pb-6';
@endphp

<x-layout title="발령 관리">
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
                ['label' => '인사', 'href' => url('/hr')],
                ['label' => '발령 관리'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">발령 관리</h1>
        </x-slot:title>

        {{-- 기준일 — 인사 관리와 같은 자리(오른쪽 끝)에 둔다 --}}
        <x-slot:actions>
            <button type="button"
                    class="inline-flex shrink-0 items-center gap-2 rounded-md p-1 text-label-1 leading-5 text-mono-black transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                <span class="font-medium">기준일</span>
                <span class="tabular-nums">2021.08.01</span>
                <x-icon-chevron-down class="size-3.5 text-label-alternative" />
            </button>
        </x-slot:actions>

        <div class="flex min-w-0 flex-col gap-6 pt-8 pb-10">
            {{-- ── 발령 기준 관리 ── --}}
            <section class="{{ $card }}">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <h2 class="{{ $cardTitle }}">발령 기준 관리</h2>
                    <p class="text-label-1 leading-5 text-label-alternative">
                        총 <span class="tabular-nums">{{ count($rules) }}</span> 건
                    </p>
                    <span class="ml-auto flex shrink-0 flex-wrap items-center gap-3">
                        <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                        <x-button variant="outline" size="sm" icon="plus">발령 기준 추가</x-button>
                    </span>
                </div>

                {{-- 열이 많아 카드보다 넓다. 카드 안쪽 폭을 꽉 채우고 가로로 넘긴다. --}}
                <div class="-mx-[30px] mt-5">
                    <x-table flush min-width="1520px" selectable
                             class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                        <x-table.head selectable :all-ids="array_map('strval', array_keys($rules))"
                                      :columns="array_merge([
                                          ['label' => '발령 유형', 'width' => '100px'],
                                          ['label' => '발령명', 'width' => '100px'],
                                          ['label' => '재직 상태', 'width' => '100px'],
                                          ['label' => '시작일', 'width' => '100px'],
                                          ['label' => '종료일', 'width' => '100px'],
                                      ], array_map(
                                          fn ($l) => ['label' => $l, 'width' => in_array($l, ['수습 종료일', '종료 예정일'], true) ? '100px' : '80px'],
                                          $flagLabels
                                      ), [
                                          ['label' => '종료 발령', 'width' => '100px'],
                                      ])" />
                        <tbody>
                            @foreach ($rules as $i => [$type, $name, $status, $flags])
                                <x-table.row selectable :value="(string) $i">
                                    <x-table.cell tone="muted" nowrap>{{ $type }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>{{ $name }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>{{ $status }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap class="tabular-nums">2021.12.30</x-table.cell>
                                    <x-table.cell tone="muted" nowrap class="tabular-nums">2021.12.30</x-table.cell>

                                    @foreach ($flags as $j => $on)
                                        {{-- 읽기 전용 표시다. DS 체크박스를 비활성으로 넣어 모양을 맞춘다. --}}
                                        <x-table.cell nowrap>
                                            <x-checkbox :checked="(bool) $on" disabled
                                                        :aria-label="$flagLabels[$j] . ($on ? ' 바뀜' : ' 안 바뀜')" />
                                        </x-table.cell>
                                    @endforeach

                                    {{-- 빈 값은 하이픈이다 --}}
                                    <x-table.cell tone="muted" nowrap>-</x-table.cell>
                                </x-table.row>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>
            </section>

            {{-- ── 수정일 관리 ── 발령이 났을 때 어떤 날짜를 다시 쓰는지 --}}
            <section class="{{ $card }}">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <h2 class="{{ $cardTitle }}">수정일 관리</h2>
                    <p class="text-label-1 leading-5 text-label-alternative">
                        총 <span class="tabular-nums">{{ count($dateRules) }}</span> 건
                    </p>
                    <span class="ml-auto flex shrink-0 flex-wrap items-center gap-3">
                        <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                        <x-button variant="outline" size="sm" icon="plus">신규 추가</x-button>
                    </span>
                </div>

                <div class="-mx-[30px] mt-5">
                    <x-table flush min-width="1000px" selectable
                             class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                        <x-table.head selectable :all-ids="array_map('strval', array_keys($dateRules))"
                                      :columns="[
                                          ['label' => '일자 구분', 'width' => '160px'],
                                          ['label' => '비고'],
                                      ]" />
                        <tbody>
                            @foreach ($dateRules as $i => $label)
                                <x-table.row selectable :value="(string) $i">
                                    <x-table.cell tone="muted" nowrap>{{ $label }}</x-table.cell>
                                    <x-table.cell tone="muted">내용 없음</x-table.cell>
                                </x-table.row>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>
            </section>
        </div>
    </x-workspace-shell>
</x-layout>
