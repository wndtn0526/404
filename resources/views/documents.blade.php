{{-- 문서 신청 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-106228)
     전자결재의 시작점. 양식을 고르면 그 양식으로 기안을 쓴다.

     원본 실측(1920) — 본문 1200 (x480~1680)
       제목 24 Bold lh34 · 밑줄 탭 다섯 — 왼쪽 정렬(칸 52 · 사이 32)이고 밑줄만 본문 폭 전체
         (DS x-tabs 는 사이가 24 라 8 좁다. block 을 주면 균등 분할이 돼서 원본과 달라진다)
       검색 카드 1200x100 · 안쪽 30 · 검색 칸 988x40 + '조회' 128x40
       양식 카드 384x195 · 3열 · 가로·세로 사이 24 (384x3 + 24x2 = 1200)
         안쪽 30 · 배지 10 Bold 반경 2 · 제목 16 Bold lh24 -0.6 (DS body-1)
         설명 14 Regular lh20 -0.2 (DS label-1) · '신청하기' 버튼 79x30 (연필 아이콘 14)

     ⚠️ 원본 검색은 입력 칸 + '조회' 버튼짜리 카드다. 컨텐츠 관리부터 쓰기로 한 공용
        x-filter-bar 로 바꿨다. 여기는 거를 항목이 없어서 '필터 추가' 는 끄고 검색만 남겼다.
     ⚠️ 배지 색이 원본은 Primary/green 900 글자 + Secondary/green 100 면인데, DS x-badge
        green 은 파생값 accent-fg-green 을 글자에 쓴다(green 900 의 70%). 배지를 손으로
        만들지 않기로 한 규칙대로 DS 것을 썼다 — 원본보다 글자가 조금 진하다.
        (뷰에 hex 를 적으면 raw hex 검사에 걸린다. 주석까지 훑는다.)
     ⚠️ 'IT & 보안' 과 '사무 지원' 은 원본에서 배지 색이 같다(Warm gray/100). 그대로 뒀다.
     ⚠️ 기안 화면이 있는 양식은 '지출 결의서 (개인 비용)' 하나뿐이다(node 1002-113826).
        나머지는 버튼을 '준비 중' 으로 잠갔다 — 눌러도 아무 일이 없으면 고장으로 읽힌다.
        화면이 생기는 대로 $forms 에 href 를 주면 풀린다.
     ⚠️ 값은 전부 예시다. DB 에서 오지 않는다. --}}
@php
    /*
     * 양식 목록. category 는 탭 필터에 쓰는 키이고, badge 는 DS x-badge 색이다.
     * ⚠️ Tailwind 는 파일을 문자열로 훑으므로 배열엔 완성된 클래스명·색 이름을 담는다.
     */
    $categories = [
        'all' => '전체 양식',
        'hr' => '인사 관련',
        'finance' => '재무 관련',
        'it' => 'IT & 보안',
        'office' => '사무 지원',
    ];

    $badgeColor = ['hr' => 'green', 'finance' => 'blue', 'it' => 'neutral', 'office' => 'neutral'];

    /*
     * href 가 있는 양식만 신청할 수 있다. 나머지는 화면이 아직 없어서 버튼을 잠근다 —
     * 눌러도 아무 일이 없으면 고장 난 것처럼 보인다. 화면이 생기면 href 를 준다.
     */
    $forms = [
        ['cat' => 'hr', 'name' => '근태 · 휴가 신청서', 'desc' => '휴가 신청이 필요할 때 작성'],
        ['cat' => 'hr', 'name' => '임신기 근로 시간 단축 신청서', 'desc' => '임신기 근로 시간 조절이 필요할 때 작성'],
        ['cat' => 'hr', 'name' => '육아기 근로 시간 단축 신청서', 'desc' => '육아기 근로 시간 조절이 필요할 때 작성'],
        ['cat' => 'finance', 'name' => '기안서', 'desc' => '일반 기안서'],
        ['cat' => 'finance', 'name' => '지출 결의서 (거래처)', 'desc' => '거래처용 지출 결의서'],
        ['cat' => 'finance', 'name' => '지출 결의서 (개인 비용)', 'desc' => '개인용 지출 결의서',
            'href' => '/documents/new'],
        ['cat' => 'finance', 'name' => '법인 카드 지급 신청서', 'desc' => '법인 카드가 필요할 때 작성'],
        ['cat' => 'it', 'name' => 'IT 비품 사용 신청서', 'desc' => 'IT 비품 사용 필요시 작성'],
        ['cat' => 'office', 'name' => '퀵 서비스 신청서', 'desc' => '퀵 서비스 이용 필요시 작성'],
        ['cat' => 'office', 'name' => '명함 신청서', 'desc' => '명함 발급 필요시 작성하는 신청서입니다.'],
        ['cat' => 'hr', 'name' => '교육 신청서', 'desc' => '교육 참여가 필요할 때 작성하는 신청서입니다.'],
        ['cat' => 'hr', 'name' => '유연 근무제 신청서', 'desc' => '유연 근무제 필요시 작성하는 신청서입니다.'],
    ];
@endphp

<x-layout title="문서 신청">
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
                ['label' => '문서 신청'],
            ]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">문서 신청</h1>
        </x-slot:title>

        {{-- 탭이 카드를 거른다. 카드에는 display 유틸을 두지 않는다 —
             hidden 과 같은 자리를 다퉈서 안 먹는다(CLAUDE.md 참고). --}}
        <div class="mt-8 min-w-0" x-data="{ cat: 'all' }">
            <x-tabs
                name="form_category"
                x-model="cat"
                :options="$categories"
                selected="all"
                accent="strong"
            />

            <x-filter-bar
                search="찾으시는 문서 이름을 검색해보세요"
                :columns="[]"
                :addable="false"
                class="pt-6"
            />

            {{-- 양식 카드 — 원본 384x195 3열 · 사이 24 --}}
            <div class="grid grid-cols-1 gap-6 pt-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($forms as $form)
                    <div class="min-w-0 rounded-lg bg-background-normal p-[30px]"
                         x-bind:class="{ 'hidden': cat !== 'all' && cat !== @js($form['cat']) }">
                        <x-badge :color="$badgeColor[$form['cat']]" size="xs">{{ $categories[$form['cat']] }}</x-badge>

                        <h2 class="pt-[11px] text-body-1 font-bold leading-6 text-mono-black">{{ $form['name'] }}</h2>
                        <p class="pt-1.5 text-label-1 leading-5 text-mono-black">{{ $form['desc'] }}</p>

                        {{-- 원본 79x30 — DS 버튼 sm 은 40 이라 한 단계 크다. --}}
                        @if (! empty($form['href']))
                            <x-button variant="primary" size="sm" icon="pencil"
                                      href="{{ url($form['href']) }}" class="mt-[18px]">신청하기</x-button>
                        @else
                            <x-button variant="primary" size="sm" icon="pencil" disabled
                                      class="mt-[18px]">준비 중</x-button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </x-workspace-shell>
</x-layout>
