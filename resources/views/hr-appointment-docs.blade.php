{{-- 발령 등록 (발령서 목록) — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-262679)
     발령서를 만들고 대상자를 붙이는 화면이다. 발령 '기준' 을 정하는 화면(node 1002-262024)과
     짝이다 — 원본 LNB 도 둘을 발령 관리 밑에 나란히 둔다.

     원본 실측(1920) — 본문 1520 (x320~1840)
       제목 24 Bold lh39
       검색·필터 카드 1520x156 · 안쪽 30 — 검색 1260x40 + 필터 40 + '조회' 128x40
         아랫줄 드롭다운 하나 296 + 초기화
       발령서 목록 카드 1520x782 · 안쪽 30
         제목 20 Bold + '총 1,000 건' · 우측 '엑셀로 저장' 93x30 + '발령 추가' 82x30
         표 10열(체크 + 9) · 머리 56 · 줄 56 · 열 줄 · 아래 작은 페이지네이션

     ⚠️ 원본은 제목 옆에 칩 탭이 일곱 개 붙는데 활성만 '발령 등록' 이고 나머지 여섯은
        인사 관리 화면의 탭이 그대로 복사돼 있다(인사 정보 조회가 두 번 나온다).
        인사 관리에서 칩을 빼기로 한 것과 같은 이유로 여기서도 뺐다 — 원본 자체가 자리표시다.
     ⚠️ 원본 검색·필터는 이 화면 전용 카드다. 컨텐츠 관리부터 쓰기로 한 공용 x-filter-bar 로 바꿨다.
     ⚠️ 원본 건수는 '총 1,000 건' 인데 줄은 열 개다. 실제로 그린 줄 수를 낸다.
     ⚠️ '엑셀로 저장'·'발령 추가' 는 아직 갈 곳이 없다. 붙일 때는 POST + CSRF 로 보내고
        권한은 화면이 아니라 Policy 에서 본다. 발령은 사람의 소속·직책을 바꾸는 일이라
        상태 전이를 한 곳에서 정의하고 그쪽을 부른다.
     ⚠️ 값은 전부 예시다. --}}
@php
    $names = ['심프로', '정프로', '오프로', '최프로', '장프로', '곽프로', '황프로', '문프로', '유프로', '한프로'];
    $kinds = ['입사발령', '부서 이동', '직책 임명', '휴직 처리', '복직 처리'];

    $docs = [];
    foreach ($names as $i => $name) {
        $docs[] = [
            'company' => 'GPRO 그룹',
            'no' => 'NCO-2021-09-'.str_pad((string) (31 + $i), 4, '0', STR_PAD_LEFT),
            'name' => '[시스템자동생성] '.$kinds[$i % count($kinds)],
            'writer' => $name,
            'created' => '2021.12.30',
            'targets' => '등록된 대상자 없음',
            // 원본은 첫 줄이 '발령 확정'(초록), 둘째 줄이 '대기중'(회색)이다.
            'done' => $i % 2 === 0,
            'file' => '첨부 파일 없음',
            'memo' => '내용 없음',
        ];
    }

    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
@endphp

<x-layout title="발령 등록">
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
                ['label' => '발령 등록'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">발령 관리</h1>
        </x-slot:title>

        <x-filter-bar
            search="발령명, 발령 번호로 검색해보세요"
            :active="['status']"
            :columns="[
                ['key' => 'status', 'label' => '발령 처리', 'type' => 'select', 'options' => ['전체', '발령 확정', '대기중']],
            ]"
            class="mt-8"
        />

        <section class="mt-6 min-w-0 rounded-lg bg-background-normal p-[30px] pb-6">
            <div class="flex min-w-0 flex-wrap items-center gap-3">
                <h2 class="{{ $cardTitle }}">발령서 목록</h2>
                <p class="text-label-1 leading-5 text-label-alternative">
                    총 <span class="tabular-nums">{{ count($docs) }}</span> 건
                </p>
                <span class="ml-auto flex shrink-0 flex-wrap items-center gap-3">
                    <x-button variant="outline" size="sm" icon="download">엑셀로 저장</x-button>
                    <x-button variant="outline" size="sm" icon="plus">발령 추가</x-button>
                </span>
            </div>

            <div class="-mx-[30px] mt-5">
                <x-table flush min-width="1520px" selectable
                         class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                    <x-table.head selectable :all-ids="array_map('strval', array_keys($docs))"
                                  :columns="[
                                      ['label' => '법인', 'width' => '120px'],
                                      ['label' => '발령 번호', 'width' => '160px'],
                                      ['label' => '발령명', 'width' => '280px'],
                                      ['label' => '작업자', 'width' => '120px'],
                                      ['label' => '작성일', 'width' => '100px'],
                                      ['label' => '대상자 등록', 'width' => '160px'],
                                      ['label' => '발령 처리', 'width' => '90px'],
                                      ['label' => '첨부파일', 'width' => '140px'],
                                      ['label' => '비고'],
                                  ]" />
                    <tbody>
                        @foreach ($docs as $i => $d)
                            <x-table.row selectable :value="(string) $i">
                                <x-table.cell tone="muted" nowrap>{{ $d['company'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $d['no'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $d['name'] }}</x-table.cell>
                                <x-table.cell nowrap>
                                    <span class="flex min-w-0 items-center gap-2.5">
                                        <x-thumbnail :name="$d['writer']" size="sm" shape="circle" class="shrink-0" />
                                        <span class="min-w-0 truncate font-bold text-label-strong">{{ $d['writer'] }}</span>
                                    </span>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap class="tabular-nums">{{ $d['created'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $d['targets'] }}</x-table.cell>
                                <x-table.cell nowrap>
                                    <x-badge :color="$d['done'] ? 'green' : 'neutral'" size="xs">
                                        {{ $d['done'] ? '발령 확정' : '대기중' }}
                                    </x-badge>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $d['file'] }}</x-table.cell>
                                <x-table.cell tone="muted">{{ $d['memo'] }}</x-table.cell>
                            </x-table.row>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <div class="mt-6">
                <x-pagination :total="count($docs)" :per-page="10" :current="1" />
            </div>
        </section>
    </x-workspace-shell>
</x-layout>
