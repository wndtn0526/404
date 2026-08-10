{{-- 예산 계정 관리 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-93118)
     재무 > 업무 관리자 메뉴의 한 탭. 왼쪽에 비용 분류, 오른쪽에 그 분류에 달린 비용 내역.

     원본 실측(1920) — 본문 1520 (좌 320 · 우 80)
       제목 '업무 관리자 메뉴' 30 Bold lh39 · 알약 탭 줄 (조직 관리 화면과 같은 모양)
       좌 카드 400x823 · 제목 20 Bold lh30 · '+ 추가' 59x30 우측 · 표는 카드 폭 전체
         프로그램 이름 260 | 과목 코드 140 = 400 · 행 56 · 12행
       우 카드 1096x782 · '+ 내역 추가' 78x26 우측
         표 내용 폭 1240 인데 카드가 1096 이라 원본도 잘려 있다 — 가로 스크롤이다
         비용 분류 160 | 계정 이름 180 | 계정 이름 (영어) 200 | 계정 코드 80 | 1인 한도 80
           | 설명 80 | 대상 인원 80 | 사용자 가이드 180 | 사용자 가이드 (영어) 200 = 1240
         행 56 · 10행 · 아래 페이지네이션 + '10개씩 보기' 92x26
       카드 사이 24 (400 + 24 + 1096 = 1520)

     ⚠️ 제목 옆 알약 줄은 이 화면의 하위 탭이 아니라 「들렀던 메뉴 바로가기」다(x-menu-tabs).
        원본 화면에 '인사 정보 조회 · 직인 및 워터마크 관리 …' 가 박혀 있는 건 그때까지
        들른 메뉴를 그려 둔 것이다. 그래서 여기서 목록을 정하지 않는다.
     ⚠️ 원본은 계정 이름과 영어 이름이 서로 어긋나 있다(미팅비 → IT computing equipment 등).
        자리표시자를 돌려 쓴 것으로 보여 여기서는 한국어에 맞는 영어로 적었다.
     ⚠️ 왼쪽 표 열 이름이 원본은 '계정 코드' 인데 '과목 코드' 로 바꿨다. 추가 팝업의 칸과
        같은 값이라 이름이 갈리면 안 된다. 오른쪽 비용 내역의 '계정 코드' 는 계정에 붙는
        코드라 그대로 뒀다 — 둘은 다른 값이다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다.
     ⚠️ '+ 추가' · '+ 내역 추가' 는 모달을 열기만 한다. 저장 엔드포인트가 없다 —
        붙일 때는 POST + CSRF 로 보낸다.

     왼쪽 비용 분류를 누르면 오른쪽이 그 분류 것만 남는다. 같은 행을 다시 누르면 전체로
     돌아간다. 고른 분류를 오른쪽 제목 옆에 칩으로도 보여 줬다가 뺐다 — 왼쪽에서 이미
     행이 칠해져 있어 같은 말을 두 번 하는 셈이었다.
     정적 화면이라 이미 그려진 행을 감추는 방식이다 — 실제로 붙일 때는 GET 파라미터로
     보내 서버에서 걸러야 페이지 수가 맞는다. --}}
@php
    // 좌 — 비용 분류. 과목 코드는 아직 안 붙였다(원본도 전부 하이픈이다).
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

    /*
     * 우 — 비용 내역. 왼쪽 비용 분류를 고르면 그 분류 것만 남는다.
     * 원본은 10행이 분류와 1:1 이라 걸러도 한 줄만 남는다. 분류마다 여러 건이 달리는 게
     * 실제 모습이라 몇 개는 여러 건으로 뒀다.
     * 사용자 가이드는 계정 이름과 같은 값이 들어간다(원본 그대로).
     */
    $rows = [
        ['group' => '교육 훈련비', 'name' => 'IT 전산 장비 (PC 등)', 'name_en' => 'IT computing equipment', 'code' => '21001'],
        ['group' => '교육 훈련비', 'name' => '외부 교육 수강료', 'name_en' => 'External training fees', 'code' => '21002'],
        ['group' => '교육 훈련비', 'name' => '자격 시험 응시료', 'name_en' => 'Certification exam fees', 'code' => '21003'],
        ['group' => '교통비', 'name' => '거래처 접대비', 'name_en' => 'Client entertainment', 'code' => '13003'],
        ['group' => '교통비', 'name' => '출장 교통비', 'name_en' => 'Business trip transportation', 'code' => '13004'],
        ['group' => '기타 수수료', 'name' => '기타 지급 수수료', 'name_en' => 'Other payment fees', 'code' => '21001'],
        ['group' => '마케팅비', 'name' => '도서구매 및 인쇄물', 'name_en' => 'Book purchase and printing', 'code' => '13003'],
        ['group' => '마케팅비', 'name' => '온라인 광고비', 'name_en' => 'Online advertising', 'code' => '13005'],
        ['group' => '복리 후생비', 'name' => '미팅비', 'name_en' => 'Meeting expenses', 'code' => '21001'],
        ['group' => '복리 후생비', 'name' => '경조사비', 'name_en' => 'Congratulations and condolences', 'code' => '21004'],
        ['group' => '비품 · 소프트웨어', 'name' => '소프트웨어 (기간 사용)', 'name_en' => 'Software subscription', 'code' => '13003'],
        ['group' => '소모품비', 'name' => '소모품비', 'name_en' => 'Consumables', 'code' => '21001'],
        ['group' => '식대', 'name' => '식대', 'name_en' => 'Meal expenses', 'code' => '13003'],
        ['group' => '조직관리비', 'name' => '조직 관리비', 'name_en' => 'Organization management', 'code' => '21001'],
        ['group' => '퀵 · 택배 등', 'name' => '통신비', 'name_en' => 'Communication expenses', 'code' => '13003'],
        ['group' => '퀵 · 택배 등', 'name' => '퀵 · 택배 요금', 'name_en' => 'Courier and delivery', 'code' => '13006'],
    ];

    // 분류별 건수 — Alpine 이 고른 분류의 「총 N건」에 쓴다.
    $countsByGroup = collect($rows)->countBy('group')->all();

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
            <x-menu-tabs menu="budget" label="예산 계정 관리" href="/finance/budget" />
        </x-slot:title>

        {{-- 왼쪽에서 고른 비용 분류가 오른쪽 표를 거른다. 두 카드가 같은 스코프를 본다.
             group : 고른 분류 이름. null 이면 전체.
             ⚠️ 거르기는 이미 그려진 행을 감추는 방식이다(정적 화면이라 재조회가 없다).
                실제로 붙일 때는 GET 파라미터로 보내 서버에서 걸러야 페이지가 맞는다. --}}
        <div class="mt-8 flex min-w-0 flex-col gap-6 pb-10 xl:flex-row xl:items-start"
             x-data="{
                 group: null,
                 counts: @js($countsByGroup),
                 pick(g) { this.group = this.group === g ? null : g; },
                 total() { return this.group === null ? {{ count($rows) }} : (this.counts[this.group] ?? 0); },
             }">

            {{-- ═══ 좌: 비용 분류 400 ═══ --}}
            <section class="w-full min-w-0 shrink-0 rounded-lg bg-background-normal pb-[30px] xl:w-[400px]">
                <div class="flex min-w-0 items-center justify-between gap-3 px-[30px] pt-[30px]">
                    <h2 class="{{ $cardTitle }}">비용 분류</h2>
                    {{-- 원본 59x30 — DS 버튼 sm 은 40 이라 한 단계 크다 --}}
                    <x-button variant="outline" size="sm" icon="plus"
                              @click="$dispatch('open-modal', 'group-add')">추가</x-button>
                </div>

                {{-- 표는 카드 폭 전체로 흘린다(원본 그대로) --}}
                <div class="pt-5">
                    <x-table min-width="400px" class="rounded-none border-x-0">
                        <x-table.head :columns="[
                            ['label' => '프로그램 이름', 'width' => '260px'],
                            ['label' => '과목 코드'],
                        ]" />
                        <tbody>
                            @forelse ($groups as $group)
                                @php
                                    // ⚠️ @js() 는 컴포넌트 속성 안에서 컴파일되지 않는다(일반 엘리먼트와 다르다).
                                    //    Alpine 식을 통째로 PHP 에서 만들어 :속성 으로 넘긴다.
                                    $g = json_encode($group['name'], JSON_UNESCAPED_UNICODE);
                                    $pickedClass = "group === {$g} ? 'bg-warm-gray-100' : ''";
                                @endphp
                                {{-- 행 전체가 고르는 버튼이다. 조직 관리 표와 같은 방식
                                     (이름 칸 버튼을 after:inset-0 로 행 전체까지 늘린다). --}}
                                <x-table.row class="relative"
                                             :x-bind:class="$pickedClass">
                                    <x-table.cell tone="strong">
                                        <button type="button" @click="pick(@js($group['name']))"
                                                x-bind:aria-pressed="group === @js($group['name'])"
                                                class="text-left after:absolute after:inset-0 focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4">
                                            {{ $group['name'] }}
                                        </button>
                                    </x-table.cell>
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
                <div class="flex min-w-0 flex-wrap items-center justify-between gap-3 px-[30px] pt-[30px]">
                    <div class="flex min-w-0 flex-wrap items-center gap-3">
                        <h2 class="{{ $cardTitle }}">비용 내역</h2>
                        <span class="text-label-1 font-medium leading-5 text-label-alternative">
                            총 <span class="tabular-nums" x-text="total()">{{ count($rows) }}</span>건
                        </span>
                    </div>
                    <x-button variant="outline" size="sm" icon="plus"
                              @click="$dispatch('open-modal', 'account-add')">내역 추가</x-button>
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
                                @php
                                    $notPicked = "{ 'hidden': group !== null && group !== ".json_encode($row['group'], JSON_UNESCAPED_UNICODE).' }';
                                @endphp
                                <x-table.row :x-bind:class="$notPicked">
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

                            {{-- 고른 분류에 내역이 하나도 없을 때 — 서버 데이터가 비어서가 아니라
                                 걸러서 비는 경우라 위 @empty 와 따로 둔다. --}}
                            <x-table.empty :colspan="9" x-cloak x-bind:class="{ 'hidden': total() > 0 }">
                                이 분류에 등록된 비용 내역이 없습니다.
                            </x-table.empty>
                        </tbody>
                    </x-table>
                </div>

                {{-- 원본은 페이지 다섯 개 + '10개씩 보기' 다.
                     분류를 고르면 걸러진 것이 한 화면에 다 나오므로 감춘다. 서버에서 거르게
                     바꾸면 그때는 걸러진 건수로 다시 그린다. --}}
                <div class="px-[30px] pt-[30px]" x-bind:class="{ 'hidden': group !== null }">
                    <x-pagination :total="50" :per-page="10" :current="1" :per-page-options="[10, 50, 100]" />
                </div>
            </section>
        </div>

        @php
            // 원본 필드 칸 315x54 · 열 사이 30 · 행 피치 78 — 조직 추가 모달과 같은 격자다.
            $modalGrid = 'grid grid-cols-1 gap-x-[30px] gap-y-6 sm:grid-cols-2';
        @endphp

        {{-- ═══ 비용 분류 추가 ═══ Figma node 1002-93353
             원본 실측 — 폭 544 · 반경 6 · 패딩 30 · 제목 20 Bold lh30 -1
               필드 484x54 한 열 · 행 피치 78
               카드 폭 구분선 → 25 → 버튼 120x36 (사이 16 · 우측)
               '추가' 는 비활성으로 그려져 있다 — 아무것도 안 넣은 상태라서다

             ⚠️ 세 번째 칸이 원본은 '지급 금액'인데 과목 코드로 바꿨다(요청). 분류에 붙는
                코드라 과목 코드가 맞다.

             ⚠️ 원본 제목 앞 📄 이모지는 빼기로 한 규칙대로 넣지 않았다.
             ⚠️ DS 모달 제목이 22 라 원본 20 보다 한 단계 크다. 이 저장소 모달 전부가 그렇다.
             ⚠️ 저장 엔드포인트가 없다. '추가'는 모달만 닫는다. 붙일 때는 POST + CSRF. --}}
        {{-- ⚠️ x-data 는 <x-modal> 에 준다. 푸터는 별도 슬롯이라 본문 안 div 에 두면
             그 스코프 밖이라 버튼이 form 을 못 본다. 모달에 주면 본문·푸터가 같이 본다. --}}
        <x-modal name="group-add" title="비용 분류 추가" max-width="max-w-[544px]" scroll close-button
                 x-data="{ form: { name: '', name_en: '', code: '' } }">
                <div class="grid grid-cols-1 gap-y-6">
                    <x-input label="비용 분류 이름" name="group_name" size="sm"
                             placeholder="비용 분류 이름 입력" x-model="form.name" />
                    <x-input label="비용 분류 이름 (영어)" name="group_name_en" size="sm"
                             placeholder="비용 분류 영어 이름 입력" x-model="form.name_en" />
                    {{-- 원본은 '지급 금액'(placeholder '0 원')인데 과목 코드로 바꿨다.
                         분류에 붙는 코드라 과목 코드가 맞고, 지급 금액은 왼쪽 표에 들어갈
                         자리도 없다(열이 '프로그램 이름 | 과목 코드' 다). --}}
                    <x-input label="과목 코드" name="group_code" size="sm"
                             placeholder="과목 코드 번호 입력" x-model="form.code" />
                </div>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">취소</x-button>
                        {{-- 이름을 넣어야 누를 수 있다(원본이 비활성으로 그려져 있다) --}}
                        <x-button variant="primary" size="sm" class="w-[120px]"
                                  x-bind:disabled="! form.name.trim()" @click="open = false">추가</x-button>
                    </div>
                </x-slot:footer>
        </x-modal>

        {{-- ═══ 비용 내역 추가 ═══ Figma node 1002-93832
             원본 실측 — 폭 720 · 반경 6 · 패딩 30
               필드 315x54 두 열 (315+30+315 = 660) · 행 피치 78
               세 줄은 두 열, 사용자 가이드 두 줄은 660 전체 폭
               비용 분류·1인 한도 금액은 드롭다운, 설정·대상 인원은 돋보기 붙은 검색 칸
               카드 폭 구분선 → 25 → 버튼 120x36

             ⚠️ 비용 분류 선택지는 왼쪽 표와 같은 목록이다. 실제로 붙을 땐 한 곳에서 온다.
             ⚠️ 1인 한도 금액은 원본이 '한도 없음' 이 들어간 드롭다운이다. 금액을 직접 넣는
                칸이 따로 필요해 보이는데 원본에 없어서 그대로 뒀다.
             ⚠️ 설정·대상 인원은 원본이 검색 칸이다. 무엇을 찾는 검색인지는 원본에 없다.
             ⚠️ 저장 엔드포인트가 없다. --}}
        <x-modal name="account-add" title="비용 내역 추가" max-width="max-w-[720px]" scroll close-button
                 x-data="{ form: { group: '', name: '', code: '', limit: '한도 없음', setting: '', target: '', guide: '', guide_en: '' } }">
                <div class="{{ $modalGrid }}">
                    <x-dropdown label="비용 분류" name="account_group" size="sm"
                                placeholder="비용 분류 선택"
                                :options="collect($groups)->pluck('name', 'name')->all()"
                                x-model="form.group" />
                    <x-input label="계정 이름" name="account_name" size="sm"
                             placeholder="계정 이름 입력" x-model="form.name" />

                    <x-input label="계정 코드" name="account_code" size="sm"
                             placeholder="계정 코드 번호 입력" x-model="form.code" />
                    <x-dropdown label="1인 한도 금액" name="account_limit" size="sm"
                                :options="['한도 없음' => '한도 없음', '월 한도' => '월 한도', '건별 한도' => '건별 한도']"
                                selected="한도 없음" x-model="form.limit" />

                    <x-input label="설정" name="account_setting" size="sm" icon="search"
                             placeholder="설정 찾기" x-model="form.setting" />
                    <x-input label="대상 인원" name="account_target" size="sm" icon="search"
                             placeholder="멤버 찾기" x-model="form.target" />

                    <div class="sm:col-span-2">
                        <x-input label="사용자 가이드" name="account_guide" size="sm"
                                 placeholder="사용자 가이드 입력" x-model="form.guide" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input label="사용자 가이드 (영어)" name="account_guide_en" size="sm"
                                 placeholder="영어 사용자 가이드 입력" x-model="form.guide_en" />
                    </div>
                </div>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">취소</x-button>
                        {{-- 분류·계정 이름·계정 코드가 있어야 누를 수 있다 --}}
                        <x-button variant="primary" size="sm" class="w-[120px]"
                                  x-bind:disabled="! (form.group && form.name.trim() && form.code.trim())"
                                  @click="open = false">추가</x-button>
                    </div>
                </x-slot:footer>
        </x-modal>
    </x-workspace-shell>
</x-layout>
