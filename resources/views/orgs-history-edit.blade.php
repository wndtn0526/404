{{-- 변경 이력 수정 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-274589 "page")
     조직 관리 > 변경 이력 탭의 '변경 이력 추가' 를 누르면 오는 화면.
     입력할 값이 많아서 팝업이 아니라 페이지로 간다(요청 그대로).

     원본 실측(1920) — 카드 792 가운데 정렬 · 반경 6 · 패딩 30
       브레드크럼 56 · 뒤로 32 + 제목(39) 106 · 카드 175
       섹션 제목 20 Bold lh30 -1 (DS heading-2 와 정확히 일치) · 제목 아래 30
       필드 351x54 · 열 사이 30 (351+30+351 = 732 = 내부 폭) · 행 피치 78
       기본 정보 뒤 내부 폭 구분선(732) → 30 → 상세 정보 제목
       마지막 필드 → 카드 폭 구분선(792) 40 → 25 → 버튼 120x36 → 30
       버튼 셋 — 좌: 이력 삭제(면 Secondary/red 100 · 글자 Primary/red 900)
                 우: 취소 · 저장 (사이 16)

     컨텐츠 추가 · 과정 추가와 같은 792 카드 레이아웃이다. 다른 건 버튼이 좌우로 나뉜 것뿐이다.

     ⚠️ 원본 제목 앞 🗂 이모지는 빼기로 한 규칙대로 넣지 않았다.
     ⚠️ '이력 삭제' 는 DS 버튼에 danger-soft 를 더해서 썼다. 꽉 찬 danger 로 두면 저장보다
        눈에 세게 들어온다 — 이 화면의 주 액션은 저장이다.
     ⚠️ 상위 조직은 청담원이 루트라 값이 없다. 원본은 일반 입력 칸인데 여기서는 disabled 로
        두고 하이픈을 보인다(상세 화면과 같은 표기).
     ⚠️ 필드 라벨이 원본 12 Medium, DS 인풋 sm 은 14 다 — 다른 추가·수정 폼과 같은 차이다.
     ⚠️ 조직 업무 변경 이력(언어·주요 업무)을 고치는 화면은 원본에 없다. 그쪽 '변경 이력 추가'
        버튼은 아직 이 화면으로 보내지 않았다 — 필드가 다르다.
     ⚠️ 저장·삭제 엔드포인트가 없다. 두 버튼 다 아직 아무 일도 하지 않는다.
        붙일 때는 POST + CSRF 로 보내고, 삭제는 x-confirm 으로 한 번 물어본다. --}}
@php
    // 조직 관리에서 고른 조직(청담원)의 현재 기록. 상세 화면 값과 같아야 한다.
    $record = [
        'name_ko' => '청담원',
        'valid' => '2021.08.01 -',
        'corp' => '청담원',
        'name_en' => 'Cheongdamwon',
        'order' => '0',
        'country' => '대한민국',
        'place' => '서울특별시 강남구',
        'kind' => '정규 조직',
        'type' => '본부',
        'created_type' => '신설',
        'visible' => '표시',
        'depth' => '01',
        'mail' => 'cdw@cdw.workspace.io',
    ];

    // 필드 두 열 — 원본 열 사이 30 · 행 피치 78(칸 54 + 24)
    $grid = 'grid grid-cols-1 gap-x-[30px] gap-y-6 sm:grid-cols-2';
    $sectionTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
@endphp

<x-layout title="변경 이력 수정">
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
        {{-- 브레드크럼·제목을 셸 슬롯이 아니라 본문에 둔다. 원본이 둘 다 카드 왼쪽 끝에
             맞춰져 있어서(가운데 792 기준), 셸의 페이지 헤더(좌우 80)에 두면 어긋난다. --}}
        <div class="mx-auto w-full min-w-0 max-w-[792px]">

            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '조직 관리', 'href' => url('/orgs')],
                ['label' => '변경 이력 수정'],
            ]" />

            <div class="flex min-w-0 items-center gap-4 pt-[30px]">
                <a href="{{ url('/orgs') }}"
                   class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                   aria-label="조직 관리로 돌아가기">
                    <x-icon-arrow-left class="size-6" />
                </a>
                <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">변경 이력 수정</h1>
            </div>

            <form method="POST" action="#" class="mt-[30px] min-w-0 rounded-lg bg-background-normal p-5 lg:p-[30px]">
                {{-- 엔드포인트가 붙을 때 @csrf 를 여기 둔다. --}}

                {{-- ═══ 기본 정보 ═══ --}}
                <h2 class="{{ $sectionTitle }}">기본 정보</h2>

                <div class="{{ $grid }} pt-[30px]">
                    <x-input label="조직 이름 (한글)" name="name_ko" size="sm" :value="$record['name_ko']" />
                    {{-- 원본은 '2021.08.01 - ' 처럼 시작일과 종료일을 한 칸에 적는다 --}}
                    <x-input label="조직 유효 기간" name="valid" size="sm" :value="$record['valid']" />

                    <x-dropdown label="법인 이름" name="corp" size="sm"
                                :options="[$record['corp'] => $record['corp']]" :selected="$record['corp']" />
                    <x-input label="조직 이름 (영어)" name="name_en" size="sm" :value="$record['name_en']" />
                </div>

                <div class="mt-[30px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- ═══ 상세 정보 ═══ --}}
                <h2 class="{{ $sectionTitle }} pt-[30px]">상세 정보</h2>

                <div class="{{ $grid }} pt-[30px]">
                    {{-- 청담원이 루트라 상위 조직이 없다 --}}
                    <x-input label="상위 조직" size="sm" value="-" disabled />
                    <x-input label="조직 순차" name="order" type="number" size="sm" :value="$record['order']" min="0" />

                    <x-dropdown label="국가" name="country" size="sm"
                                :options="[$record['country'] => $record['country']]" :selected="$record['country']" />
                    <x-dropdown label="근무지" name="place" size="sm"
                                :options="[$record['place'] => $record['place']]" :selected="$record['place']" />

                    <x-dropdown label="조직 종류" name="kind" size="sm"
                                :options="['정규 조직' => '정규 조직', '임시 조직' => '임시 조직']"
                                :selected="$record['kind']" />
                    <x-dropdown label="조직 유형" name="type" size="sm"
                                :options="['본부' => '본부', '실' => '실', '팀' => '팀', '스쿼드' => '스쿼드']"
                                :selected="$record['type']" />

                    {{-- 아직 소멸하지 않은 조직이라 비어 있다 --}}
                    <x-dropdown label="소멸 유형" name="closed_type" size="sm"
                                :options="['합병' => '합병', '분할' => '분할', '폐지' => '폐지']"
                                placeholder="소멸 유형 선택" />
                    <x-dropdown label="생성 유형" name="created_type" size="sm"
                                :options="['신설' => '신설', '분할' => '분할', '합병' => '합병']"
                                :selected="$record['created_type']" />

                    <x-dropdown label="조직도 표시" name="visible" size="sm"
                                :options="['표시' => '표시', '미표시' => '미표시']" :selected="$record['visible']" />
                    <x-dropdown label="조직 계층" name="depth" size="sm"
                                :options="['01' => '01', '02' => '02', '03' => '03']" :selected="$record['depth']" />

                    <x-input label="그룹 메일" name="mail" size="sm" :value="$record['mail']" />
                    <x-input label="비고" name="note" size="sm" placeholder="내용 입력" />
                </div>

                {{-- 하단 구분선 — 원본은 카드 폭(792) 전체를 지난다. 패딩 밖으로 뺀다. --}}
                <div class="-mx-5 mt-10 h-px bg-warm-gray-100 lg:-mx-[30px]" aria-hidden="true"></div>

                {{-- 버튼 — 삭제는 왼쪽, 취소·저장은 오른쪽(원본 그대로) --}}
                <div class="flex flex-wrap items-center gap-4 pt-[25px]">
                    <x-button variant="danger-soft" size="sm" class="w-[120px]">이력 삭제</x-button>

                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" href="{{ url('/orgs') }}" class="w-[120px]">취소</x-button>
                        <x-button variant="primary" size="sm" type="button" class="w-[120px]">저장</x-button>
                    </div>
                </div>
            </form>
        </div>
    </x-workspace-shell>
</x-layout>
