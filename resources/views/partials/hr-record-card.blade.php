{{-- 인사 상세의 '○○ 관리' 표 카드 — Figma GPRO_PORTFOLIO
       주요 인사 정보 node 1002-276033 · 기타 인사 정보 node 1002-276227
     같은 모양이 열몇 번 되풀이돼서 조각으로 뺐다.

     원본 실측 — 카드 반경 6 · 안쪽 30 · 제목 20 Bold lh30
       우측 버튼: [주민번호 표시 토글] [엑셀로 저장] [+ 추가] [저장(비활성)]
       표는 카드 안쪽 폭을 꽉 채우고 첫 칸만 좌 패딩 30
       비었을 땐 '아직 입력 내용이 없습니다.' 한 줄

     ⚠️ '저장' 은 원본이 비활성이다 — 고친 게 없으면 누를 수 없다는 뜻이다. 그대로 뒀다.
     ⚠️ 버튼은 아직 갈 곳이 없다. 붙일 때는 POST + CSRF 로 보내고 권한은 Policy 에서 본다.
        '엑셀로 저장' 은 개인정보가 내려가므로 담당자 확인부터 한다(CLAUDE.md).

     받는 값
       title    : 카드 제목
       columns  : 표 머리. 문자열 또는 ['label'=>, 'width'=>]
       actions  : ['excel','add','save'] 중 쓰는 것만
       rrn      : true 면 '주민번호 표시' 토글을 왼쪽에 붙인다
       select   : true 면 맨 앞에 체크박스 열
       rows     : 줄 배열(각 줄은 열 순서대로 값 배열). 없으면 빈 표. --}}
@php
    $actions = $actions ?? [];
    $rows = $rows ?? [];
    $select = $select ?? false;
    $rrn = $rrn ?? false;
    $ids = array_map(fn ($i) => (string) $i, array_keys($rows));
@endphp

<section class="min-w-0 rounded-lg bg-background-normal p-[30px] pb-6">
    <div class="flex min-w-0 flex-wrap items-center gap-3">
        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">{{ $title }}</h2>

        <div class="ml-auto flex shrink-0 flex-wrap items-center gap-3">
            @if ($rrn)
                {{-- 주민번호는 고유식별정보다. 기본은 가려 두고 눌러야 보인다. --}}
                <span class="flex items-center gap-3">
                    <span class="text-label-1 leading-5 text-mono-black">주민번호 표시</span>
                    <x-switch size="sm" x-model="showRrn" />
                </span>
            @endif

            @if (in_array('excel', $actions, true))
                <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
            @endif
            @if (in_array('add', $actions, true))
                <x-button variant="outline" size="sm" icon="plus">추가</x-button>
            @endif
            @if (in_array('save', $actions, true))
                <x-button variant="secondary" size="sm" disabled>저장</x-button>
            @endif
        </div>
    </div>

    <div class="-mx-[30px] mt-5">
        <x-table flush :min-width="$minWidth ?? '1140px'"
                 :selectable="$select"
                 class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
            <x-table.head :columns="$columns" :selectable="$select" :all-ids="$ids" />
            <tbody>
                @forelse ($rows as $i => $row)
                    <x-table.row :selectable="$select" :value="(string) $i">
                        @foreach ($row as $cell)
                            <x-table.cell tone="muted" nowrap>{{ $cell }}</x-table.cell>
                        @endforeach
                    </x-table.row>
                @empty
                    <x-table.empty :colspan="count($columns) + ($select ? 1 : 0)">아직 입력 내용이 없습니다.</x-table.empty>
                @endforelse
            </tbody>
        </x-table>
    </div>
</section>
