{{-- 지출 현황 대시보드 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-88730)
     재무 영역의 첫 화면. 필터 + 차트 넉 장 + 정산 대기 표 둘.

     원본 실측(1920) — 본문 1520 (좌 320 · 우 80)
       차트 카드 748x373 · 가로 사이 24 · 세로 사이 16 (748+24+748 = 1520)
       표 카드 748x726 · 열 180 | 150 | 150 | 150 | 118 = 748 · 행 56 · 10행
       카드 제목 20 Bold lh30 (DS heading-2) · 카드 안쪽 30

     차트 규격은 x-chart.* 주석에 적었다. 원본에 차트가 이 화면뿐이라 DS 에 규격이 없다.

     ⚠️ 비용 항목이 원본은 GPRO 것(게임 제작 · 식재료 관리 · 웹툰 …)이다. 여기서는 청담원이
        쓸 만한 항목으로 바꿨다 — 색과 개수(6종)는 원본 그대로다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다.
     ⚠️ 필터·초기화·정산하기는 아직 동작하지 않는다. 정산은 상태를 바꾸는 요청이므로
        붙일 때 POST + CSRF 로 보내고 권한은 Policy 에서 본다.
     ⚠️ 말풍선은 원본에서 마우스를 올린 지점에 뜬다. 정적 화면이라 강조한 달 옆에 세워 뒀다. --}}
@php
    /*
     * 비용 항목 여섯. 색은 원본 범례(node 1002-88919)에서 그대로 가져왔다 —
     * Cool gray/800 · purple 900 · deep blue 900 · blue 900 · bluegreen 900 · green 800.
     * ⚠️ Tailwind 는 파일을 문자열로 훑으므로 완성된 클래스명을 담는다.
     */
    $categories = [
        ['label' => '콘텐츠 제작', 'class' => 'text-cool-gray-800'],
        ['label' => '강사료', 'class' => 'text-purple-900'],
        ['label' => '시스템 운영', 'class' => 'text-deep-blue-900'],
        ['label' => '마케팅', 'class' => 'text-blue-900'],
        ['label' => '사무실', 'class' => 'text-bluegreen-900'],
        ['label' => '외주 용역', 'class' => 'text-green-800'],
    ];

    $months = ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'];

    // 꺾은선 — 1~9월만 값이 있다(원본도 9월까지 그린다). 단위 천원.
    $trend = [4200, 5600, 5100, 7300, 8800, 8200, 11500, 13800, 16100, null, null, null];
    $trendMax = 25000;
    $trendYLabels = [
        ['at' => 1.0, 'text' => '25,000k'],
        ['at' => 0.4, 'text' => '10,000k'],
    ];

    // 막대 — 1~5월은 값이 없어 축 위 토막만 남고, 6~9월만 쌓인다(원본 그대로).
    $stack = function (array $values) use ($categories) {
        $out = [];
        foreach ($values as $i => $v) {
            $out[] = ['value' => $v, 'class' => $categories[$i]['class']];
        }

        return $out;
    };
    $empty = [['value' => 0, 'class' => 'text-warm-gray-200']];

    $vendorBars = [
        $empty, $empty, $empty, $empty, $empty,
        $stack([2400, 3000, 1900, 1300, 900, 600]),
        $stack([1700, 2100, 1300, 900, 600, 400]),
        $stack([2400, 3000, 1900, 1300, 900, 600]),
        $stack([2900, 3400, 2200, 1500, 1000, 700]),
        [], [], [],
    ];
    $personalBars = [
        $empty, $empty, $empty, $empty, $empty,
        $stack([900, 1400, 700, 400, 300, 200]),
        $stack([1100, 1700, 900, 500, 400, 300]),
        $stack([1000, 1500, 800, 500, 350, 250]),
        $stack([1500, 2100, 1100, 700, 500, 350]),
        [], [], [],
    ];
    $barMax = 25000;
    $barYLabels = [['at' => 0.4, 'text' => '10,000k']];

    // 도넛 — 비율. 원본 말풍선이 33% 를 가리킨다.
    $ratio = [
        ['label' => '콘텐츠 제작', 'value' => 33, 'class' => 'text-cool-gray-800'],
        ['label' => '강사료', 'value' => 22, 'class' => 'text-purple-900'],
        ['label' => '시스템 운영', 'value' => 16, 'class' => 'text-deep-blue-900'],
        ['label' => '마케팅', 'value' => 12, 'class' => 'text-blue-900'],
        ['label' => '사무실', 'value' => 10, 'class' => 'text-bluegreen-900'],
        ['label' => '외주 용역', 'value' => 7, 'class' => 'text-green-800'],
    ];

    // 정산 대기 — 원본은 같은 행을 열 번 되풀이한 자리표시자다. 번호만 다르게 뒀다.
    $pending = function (string $prefix) {
        $rows = [];
        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                'no' => $prefix.'-210713-'.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'owner' => '청담원',
                'used_at' => '2021.09.'.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'amount' => number_format(66_000_000 - $i * 1_350_000).' 원',
            ];
        }

        return $rows;
    };
    $vendorPending = $pending('CDW');
    $personalPending = $pending('PSN');

    $card = 'min-w-0 rounded-lg bg-background-normal p-[30px]';
    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
    // 제목 옆 증감 — 원본 12 (lh17) + fold_8 화살표
    $delta = 'inline-flex items-center gap-1 text-caption-1 leading-[17px] text-label-alternative';
@endphp

<x-layout title="지출 현황 대시보드">
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
                ['label' => '재무'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">지출 현황 대시보드</h1>
            <x-menu-tabs menu="finance" label="지출 현황 대시보드" href="/finance" />
        </x-slot:title>

        {{-- ═══ 필터 ═══
             원본(1002-88730)은 1520x80 흰 카드 안에 '기간 296 · 법인 296 · 초기화' 를 넣는데,
             컨텐츠 관리·과정 관리가 이미 DS x-filter-bar 를 쓰고 있다. 화면마다 필터 생김새가
             갈리면 같은 일을 하는 자리가 달라 보여서 공용 쪽으로 맞췄다.
             카드도 같이 없앴다 — x-filter-bar 는 툴바라 카드 안에 넣을 물건이 아니다.

             ⚠️ 검색창은 붙이지 않았다. 원본 필터에 검색이 없고, 아래에 표가 둘이라 무엇을
                검색하는지가 모호해진다. 컨텐츠 관리는 표가 하나여서 붙어 있는 것이다.
             ⚠️ 「총 N건」도 뺐다. 표마다 제목 옆에 이미 있다.
             ⚠️ 정적 화면이라 실제 재조회는 없다. 감싸는 GET form 이 없어 submit() 이 그냥 지나간다. --}}
        <x-filter-bar
            :active="['period', 'corp']"
            :columns="[
                ['key' => 'period', 'label' => '기간', 'type' => 'date'],
                ['key' => 'corp', 'label' => '법인', 'type' => 'select', 'options' => ['청담원']],
            ]"
            class="mt-8 pb-3"
        />

        {{-- ═══ 차트 ═══ 원본 748x373 · 가로 24 · 세로 16 --}}
        <div class="grid min-w-0 grid-cols-1 gap-x-6 gap-y-4 xl:grid-cols-2">

            {{-- 거래처 · 개인 비용 추이 --}}
            <section class="{{ $card }}">
                <div class="flex min-w-0 flex-wrap items-center gap-3">
                    <h2 class="{{ $cardTitle }}">거래처 · 개인 비용 추이</h2>
                    <span class="{{ $delta }}">
                        <x-icon-caret-up class="size-2" />
                        전 달 대비 30,000
                    </span>
                </div>

                <div class="relative pt-[18px]">
                    <x-chart.line :labels="$months" :values="$trend" :max="$trendMax"
                                  :highlight="8" :y-labels="$trendYLabels" />

                    {{-- 원본 말풍선은 9월 점 오른쪽에 선다 --}}
                    <x-chart.tooltip title="2021.09" label="전체" value="130,452"
                                     class="absolute left-[72%] top-[24%]" />
                </div>
            </section>

            {{-- 거래처 · 개인 비용 비율 --}}
            <section class="{{ $card }}">
                <div class="flex min-w-0 flex-wrap items-start justify-between gap-3">
                    <h2 class="{{ $cardTitle }}">거래처 · 개인 비용 비율</h2>
                    <x-chart.legend :items="$categories" class="pt-1.5" />
                </div>

                <div class="relative flex min-w-0 items-center justify-center pt-6">
                    <x-chart.donut :slices="$ratio" />
                    <x-chart.tooltip title="콘텐츠 제작 (33%)" value="8,000"
                                     class="absolute left-[calc(50%+72px)] top-[26%]" />
                </div>

                <p class="pt-4 text-right text-caption-1 leading-[17px] text-label-alternative">선택된 날짜 2021년 9월</p>
            </section>

            {{-- 거래처 비용 추이 --}}
            <section class="{{ $card }}">
                <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                    <div class="flex min-w-0 flex-wrap items-center gap-3">
                        <h2 class="{{ $cardTitle }}">거래처 비용 추이</h2>
                        <span class="{{ $delta }}">
                            <x-icon-caret-up class="size-2" />
                            전 달 대비 30,000
                        </span>
                    </div>
                    <x-chart.legend :items="$categories" class="pt-1.5" />
                </div>

                <div class="relative pt-[18px]">
                    <x-chart.bars :labels="$months" :groups="$vendorBars" :max="$barMax" :y-labels="$barYLabels" />
                    <x-chart.tooltip title="강사료 (33%)" value="3,000"
                                     class="absolute left-[76%] top-[42%]" />
                </div>
            </section>

            {{-- 개인 비용 추이 --}}
            <section class="{{ $card }}">
                <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                    <div class="flex min-w-0 flex-wrap items-center gap-3">
                        <h2 class="{{ $cardTitle }}">개인 비용 추이</h2>
                        <span class="{{ $delta }}">
                            <x-icon-caret-up class="size-2" />
                            전 달 대비 30,000
                        </span>
                    </div>
                    {{-- 원본은 이 카드만 범례가 셋이다 --}}
                    <x-chart.legend :items="array_slice($categories, 0, 3)" class="pt-1.5" />
                </div>

                <div class="relative pt-[18px]">
                    <x-chart.bars :labels="$months" :groups="$personalBars" :max="$barMax" :y-labels="$barYLabels" />
                    <x-chart.tooltip title="강사료 (60%)" value="5,000"
                                     class="absolute left-[76%] top-[52%]" />
                </div>
            </section>
        </div>

        {{-- ═══ 정산 대기 목록 ═══ 원본 748x726 --}}
        <div class="mt-4 grid min-w-0 grid-cols-1 gap-x-6 gap-y-4 pb-10 xl:grid-cols-2">
            @foreach ([['거래처 정산 대기 목록', $vendorPending], ['개인 정산 대기 목록', $personalPending]] as [$title, $rows])
                <section class="min-w-0 rounded-lg bg-background-normal pb-[30px]">
                    <div class="flex min-w-0 flex-wrap items-baseline gap-4 px-[30px] pt-[30px]">
                        <h2 class="{{ $cardTitle }}">{{ $title }}</h2>
                        <span class="text-label-1 font-medium leading-5 text-label-alternative">
                            총 {{ number_format(count($rows) * 100) }} 건
                        </span>
                    </div>

                    {{-- 표는 카드 폭 전체로 흘린다(원본 그대로) --}}
                    <div class="pt-5">
                        <x-table min-width="748px" class="rounded-none border-x-0">
                            <x-table.head :columns="[
                                ['label' => '업무 번호', 'width' => '180px'],
                                ['label' => '귀속처', 'width' => '150px'],
                                ['label' => '사용 날짜', 'width' => '150px'],
                                ['label' => '금액', 'width' => '150px'],
                                ['label' => '처리'],
                            ]" />
                            <tbody>
                                @forelse ($rows as $row)
                                    <x-table.row>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['no'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="muted" nowrap>{{ $row['owner'] }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $row['used_at'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="strong" nowrap>
                                            <span class="tabular-nums">{{ $row['amount'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell nowrap>
                                            {{-- 정산은 상태를 바꾸는 일이라 링크가 아니라 버튼이다.
                                                 엔드포인트가 붙으면 POST + CSRF 로 보낸다. --}}
                                            <button type="button"
                                                    class="text-body-2 text-blue-900 transition-opacity hover:opacity-70 focus:outline-none focus-visible:underline">
                                                정산하기
                                            </button>
                                        </x-table.cell>
                                    </x-table.row>
                                @empty
                                    <x-table.empty :colspan="5">정산할 내역이 없습니다.</x-table.empty>
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </section>
            @endforeach
        </div>
    </x-workspace-shell>
</x-layout>
