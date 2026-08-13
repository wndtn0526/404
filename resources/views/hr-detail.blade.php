{{-- 인사 상세정보 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes)
       기본 정보      node 1002-275959
       주요 인사 정보  node 1002-276033
       기타 인사 정보  node 1002-276227
     인사 관리 표에서 줄을 누르면 온다.

     원본 실측(1920) — 본문 1200 (x360~1560)
       뒤로 32 · 프로필 70 · 이름 24 Bold lh39 · 탭 셋(밑줄) · 우측 아이콘 둘(내보내기 · 더보기)
       카드 반경 6 · 안쪽 30 · 절 제목 20 Bold lh30 · 소제목 14 Bold
       읽기 칸 — 라벨 94 + 값(왼쪽 열 436 · 오른쪽 열 460) · 줄 간격 36 · 열 사이 60

     ⚠️ 세 번째 탭 이름은 원본이 '기타 인사 정보' 다. '개인 인사 정보' 로 부르기도 하는데
        화면에 찍힌 글자를 따랐다.
     ⚠️ 탭 2·3 은 원본이 편집 폼이다. 저장할 곳이 없어 화면만 있다. 붙일 때는 POST + CSRF 로
        보내고 권한은 화면이 아니라 Policy 에서 본다.
     ⚠️ 표 카드가 열몇 개 되풀이돼서 partials/hr-record-card 로 뺐다.

     ══ 개인정보 ══
     이 화면은 주민등록번호(고유식별정보) · 가족의 주민등록번호 · 계좌 번호 · 여권/비자 번호 ·
     장애/보훈 정보(민감정보)를 한자리에 모은다. CLAUDE.md 와 조직 지침에 따라

       · 지금 화면에 있는 값은 전부 자리수만 맞춘 예시다. 실제 형식의 번호를 저장소·정적
         배포본에 남기지 않았다.
       · '주민번호 표시' 는 기본이 꺼짐이다. 원본도 가려 둔 채로 시작한다.
       · 실제로 붙일 때는 담당자 확인부터. 주민번호는 평문 저장·평문 로그 금지 —
         암호화 + blind index, 조회 권한은 Policy, 열람 기록을 남긴다.
       · '엑셀로 저장' 은 개인정보를 통째로 내보낸다. 권한과 감사 로그가 먼저다. --}}
@php
    $member = [
        'name' => '심프로',
        'en' => 'GPRO',
        // ⚠️ 실제 형식의 주민번호를 저장소에 남기지 않는다. 자리수만 맞춘 0 이다.
        'rrn' => '000000 - 0000000',
        'birth' => '2021.09.01',
        'employment' => '정규직',
        'entry' => '경력직',
        'route' => '잡 포털 지원',
        'nationality' => '내국인',
        'marriage' => '미혼',
        'military' => '예비역',
        'veteran' => '해당 사항 없음',
        'disability' => '해당 사항 없음',
        'company' => 'GPRO 그룹',
        'emp_no' => 'GPR200101',
        'status' => '재직중',
        'joined' => '2021.12.29',
    ];

    // 카드 제목 아래 소제목
    $subTitle = 'text-label-1 font-bold leading-5 text-mono-black';
    $card = 'min-w-0 rounded-lg bg-background-normal p-[30px]';
    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
@endphp

<x-layout title="{{ $member['name'] }} 님 · 인사 상세">
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
                ['label' => '인사 상세'],
            ]" />
        </x-slot:breadcrumb>

        {{-- 셸의 제목 슬롯을 쓰지 않는다. 원본은 뒤로 화살표 · 프로필 · 이름이 한 줄이다. --}}
        <div class="min-w-0 pt-[30px]"
             x-data="{ tab: 'basic', showRrn: false }">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ url('/hr') }}"
                   class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                   aria-label="인사 관리로 돌아가기">
                    <x-icon-arrow-left class="size-[22px]" />
                </a>
                <x-thumbnail :name="$member['name']" size="xl" shape="circle" class="shrink-0" />
                <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">{{ $member['name'] }} 님</h1>
            </div>

            {{-- 탭 줄 — 오른쪽 끝에 내보내기·더보기 --}}
            <div class="mt-[34px] flex min-w-0 items-end gap-4">
                <div class="min-w-0 flex-1">
                    <x-tabs name="hr_detail_tab" x-model="tab" accent="strong"
                            :options="['basic' => '기본 정보', 'major' => '주요 인사 정보', 'etc' => '기타 인사 정보']"
                            selected="basic" />
                </div>
                <div class="flex shrink-0 items-center gap-2 pb-1">
                    <button type="button" aria-label="내보내기"
                            class="inline-flex size-6 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                        <x-icon-upload class="size-6" />
                    </button>
                    <button type="button" aria-label="더보기"
                            class="inline-flex size-6 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                        <x-icon-more-horizontal class="size-6" />
                    </button>
                </div>
            </div>

            {{-- ═══ 탭 1 · 기본 정보 ═══ 읽기 전용 --}}
            <div x-show="tab === 'basic'" x-cloak class="pt-6 pb-10">
                <section class="{{ $card }}">
                    <div class="flex min-w-0 flex-wrap items-center gap-3">
                        <h2 class="{{ $cardTitle }}">1. 개인 정보</h2>
                        <span class="ml-auto flex shrink-0 items-center gap-3">
                            <span class="text-label-1 leading-5 text-mono-black">주민번호 표시</span>
                            <x-switch size="sm" x-model="showRrn" />
                        </span>
                    </div>

                    <p class="{{ $subTitle }} pt-8">일반</p>
                    <dl class="grid grid-cols-1 gap-x-[60px] gap-y-4 pt-3 lg:grid-cols-2">
                        <x-detail-field label="이름" :value="$member['name']" />
                        <x-detail-field label="영어 이름" :value="$member['en']" />
                        {{-- 가린 값과 드러낸 값을 둘 다 서버에서 내지 않는다 — 드러낸 쪽만 토글로 바꾼다.
                             ⚠️ 실제로 붙을 때는 '표시' 를 누른 순간 권한을 확인하고 서버에서 받아온다.
                                지금처럼 화면에 값을 들고 있으면 안 된다. --}}
                        <x-detail-field label="주민등록번호">
                            <span class="tabular-nums"
                                  x-text="showRrn ? @js($member['rrn']) : @js(mb_substr($member['rrn'], 0, 9)) + '●●●●●●'"></span>
                        </x-detail-field>
                        <x-detail-field label="생년월일" :value="$member['birth']" />
                        <x-detail-field label="고용 구분" :value="$member['employment']" />
                        <x-detail-field label="입사 구분" :value="$member['entry']" />
                        <x-detail-field label="입사 경로" :value="$member['route']" />
                        <x-detail-field label="내 · 외국인" :value="$member['nationality']" />
                    </dl>

                    <p class="{{ $subTitle }} pt-8">기타</p>
                    <dl class="grid grid-cols-1 gap-x-[60px] gap-y-4 pt-3 lg:grid-cols-2">
                        <x-detail-field label="결혼 여부" :value="$member['marriage']" />
                        <x-detail-field label="병역 여부" :value="$member['military']" />
                        <x-detail-field label="보훈 대상자" :value="$member['veteran']" />
                        <x-detail-field label="장애 여부" :value="$member['disability']" />
                    </dl>

                    {{-- 원본 구분선 1140x1 --}}
                    <div class="mt-[38px] h-px bg-line-solid-neutral"></div>

                    <h2 class="{{ $cardTitle }} pt-10">2. 인사 정보</h2>

                    <p class="{{ $subTitle }} pt-8">일반</p>
                    <dl class="grid grid-cols-1 gap-x-[60px] gap-y-4 pt-3 lg:grid-cols-2">
                        <x-detail-field label="법인" :value="$member['company']" />
                        <x-detail-field label="사번" :value="$member['emp_no']" />
                        <x-detail-field label="재직 상태" :value="$member['status']" />
                        <x-detail-field label="입사일" :value="$member['joined']" />
                    </dl>

                    <p class="{{ $subTitle }} pt-8">날짜 기록 관련</p>
                    <dl class="grid grid-cols-1 gap-x-[60px] gap-y-4 pt-3 lg:grid-cols-2">
                        <x-detail-field label="그룹 입사일" value="2021.12.29" />
                        <x-detail-field label="근태 기준일" value="2021.12.29" />
                        <x-detail-field label="최종 이동일" value="2021.12.29" />
                        <x-detail-field label="최종 보임일" value="2021.12.29" />
                        <x-detail-field label="수습 만료일" value="2021.12.29" />
                        <span class="hidden lg:block"></span>
                        <x-detail-field label="퇴직일" value="해당 사항 없음" />
                        <x-detail-field label="퇴직 사유" value="해당 사항 없음" />
                    </dl>
                </section>
            </div>

            {{-- ═══ 탭 2 · 주요 인사 정보 ═══ 편집 폼 + 관리 표 --}}
            <div x-show="tab === 'major'" x-cloak class="flex min-w-0 flex-col gap-6 pt-6 pb-10">
                <section class="{{ $card }}">
                    <div class="flex min-w-0 flex-wrap items-center gap-3">
                        <h2 class="{{ $cardTitle }}">HR 관리</h2>
                        <span class="ml-auto flex shrink-0 flex-wrap items-center gap-3">
                            <span class="flex items-center gap-3">
                                <span class="text-label-1 leading-5 text-mono-black">주민번호 표시</span>
                                <x-switch size="sm" x-model="showRrn" />
                            </span>
                            <x-button variant="primary" size="sm" icon="check">변경 내용 저장</x-button>
                        </span>
                    </div>

                    <p class="{{ $subTitle }} pt-8">필수 입력 정보</p>
                    <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 pt-3 md:grid-cols-2 xl:grid-cols-3">
                        <x-input label="주민 등록 번호" size="sm"
                                 x-bind:value="showRrn ? @js($member['rrn']) : @js(mb_substr($member['rrn'], 0, 9)) + '●●●●●●'" readonly />
                        <x-input label="사번" size="sm" :value="$member['emp_no']" />
                        <x-input label="입사일" size="sm" value="2021.12.29" />
                        <x-input label="그룹 입사일" size="sm" value="2021.12.29" />
                    </div>

                    <p class="{{ $subTitle }} pt-8">계약 관련 정보</p>
                    <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 pt-3 md:grid-cols-2 xl:grid-cols-3">
                        <x-dropdown label="입사 구분" size="sm" selected="경력직"
                                    :options="['경력직' => '경력직', '신입' => '신입']" />
                        <x-dropdown label="입사 경로" size="sm" selected="잡 포털 지원"
                                    :options="['잡 포털 지원' => '잡 포털 지원', '사내 추천' => '사내 추천', '헤드헌팅' => '헤드헌팅']" />
                        <x-input label="인정 경력" size="sm" value="10년 10개월" />
                        <x-input label="수습 만료일" size="sm" value="2021.12.29" />
                        <x-input label="퇴직 기준일" size="sm" value="2021.12.29" />
                        <x-dropdown label="퇴직일" size="sm" placeholder="날짜 선택" :options="[]" disabled />
                        <x-dropdown label="퇴직 사유" size="sm" placeholder="퇴직 사유 선택" :options="[]" disabled />
                    </div>

                    <p class="{{ $subTitle }} pt-8">기타 HR 정보</p>
                    <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 pt-3 md:grid-cols-2 xl:grid-cols-3">
                        <x-input label="근태 기준일" size="sm" value="2021.12.29" />
                        <x-input label="최종 보임일" size="sm" value="2021.12.29" />
                        <x-input label="최종 이동일" size="sm" value="2021.12.29" />
                    </div>
                </section>

                @include('partials.hr-record-card', [
                    'title' => '발령 관리',
                    'actions' => ['excel'],
                    'minWidth' => '1600px',
                    'columns' => ['발령일', '발령 유형', '발령 이름', '법인', '소속', '직책', '고용 구분',
                        '근무지', '겸직 소속', '겸직 직책', '파견 국가', '파견 소속', '파견 직책'],
                    'rows' => [
                        ['2021.12.30', '보직', '사외 겸직', 'GPRO 그룹', 'GPRO 그룹', '본부장', '정규직', '서울', 'GPRO 그룹', '본부장', '내용 없음', '내용 없음', '내용 없음'],
                        ['2021.12.30', '보직', '보직', 'GPRO 그룹', 'GPRO 그룹', '팀장', '정규직', '서울', 'GPRO 그룹', '팀장', '내용 없음', '내용 없음', '내용 없음'],
                        ['2021.12.30', '보직', '사외 겸직', 'GPRO 그룹', 'GPRO 그룹', '실장', '정규직', '서울', 'GPRO 그룹', '실장', '내용 없음', '내용 없음', '내용 없음'],
                    ],
                ])

                @include('partials.hr-record-card', [
                    'title' => '계약 관리', 'actions' => ['excel', 'add', 'save'], 'select' => true,
                    'columns' => ['계약 시작일', '계약 종료일', '계약 차수', '계약일', 'OT 지급', '계약서', '비고'],
                    'rows' => [['2021.12.30', '2021.12.30', '1차', '2021.12.30', '지급', '계약서.pdf', '내용 없음']],
                ])

                @include('partials.hr-record-card', [
                    'title' => '직무 관리', 'actions' => ['excel'],
                    'columns' => ['직군', '직종', '직무', '시작일', '종료일', '법인', '소속', '비고'],
                ])

                @include('partials.hr-record-card', [
                    'title' => '상벌점 관리', 'actions' => ['excel', 'add', 'save'], 'select' => true,
                    'columns' => ['상벌 종류', '사내외 구분', '상벌 유형', '시작일', '종료일', '징계 말소일', '첨부 파일', '비고'],
                ])

                {{-- ⚠️ 원본 '경력 관리' 표의 열이 상벌점 관리와 똑같다. 디자인 실수로 보이지만
                     고쳐 적으면 원본과 달라져서 그대로 뒀다. 디자이너 확인이 필요하다. --}}
                @include('partials.hr-record-card', [
                    'title' => '경력 관리', 'actions' => ['excel', 'add', 'save'], 'select' => true,
                    'columns' => ['상벌 종류', '사내외 구분', '상벌 유형', '시작일', '종료일', '징계 말소일', '첨부 파일', '비고'],
                ])

                @include('partials.hr-record-card', [
                    'title' => '학력 관리', 'actions' => ['excel', 'add', 'save'], 'select' => true,
                    'columns' => ['학력', '학교 이름', '전공', '부전공', '졸업 여부', '학위', '입학 날짜', '졸업 날짜'],
                ])
            </div>

            {{-- ═══ 탭 3 · 기타 인사 정보 ═══ --}}
            <div x-show="tab === 'etc'" x-cloak class="flex min-w-0 flex-col gap-6 pt-6 pb-10">
                <section class="{{ $card }}">
                    <h2 class="{{ $cardTitle }}">신상 정보</h2>

                    <p class="{{ $subTitle }} pt-8">필수 입력 신상 정보</p>
                    <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 pt-3 lg:grid-cols-2">
                        <x-input label="이름" size="sm" :value="$member['name']" />
                        <x-input label="영어 이름" size="sm" :value="$member['en']" />
                        <x-dropdown label="국적" size="sm" selected="대한민국"
                                    :options="['대한민국' => '대한민국', '미국' => '미국', '일본' => '일본']" />
                        <x-dropdown label="거주지역" size="sm" selected="대한민국"
                                    :options="['대한민국' => '대한민국', '미국' => '미국', '일본' => '일본']" />
                        <x-dropdown label="급여 계좌" size="sm" selected="GPRO 뱅크"
                                    :options="['GPRO 뱅크' => 'GPRO 뱅크', '국민은행' => '국민은행']" />
                        {{-- ⚠️ 계좌 번호도 실제 형식으로 두지 않았다 --}}
                        <x-input label="계좌 번호" size="sm" value="0000 00 0000000" />
                    </div>

                    <p class="{{ $subTitle }} pt-8">기타 신상 정보</p>
                    <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 pt-3 lg:grid-cols-2">
                        <x-dropdown label="결혼 여부" size="sm" selected="미혼"
                                    :options="['미혼' => '미혼', '기혼' => '기혼']" />
                        <x-input label="회사 메일" size="sm" value="GPRO@groupware.pro" />
                        <x-dropdown label="직무 변경일" size="sm" placeholder="날짜 선택" :options="[]" disabled />
                        <x-dropdown label="직급 진행일" size="sm" placeholder="날짜 선택" :options="[]" disabled />
                        <x-dropdown label="경비 계좌" size="sm" selected="GPRO 뱅크"
                                    :options="['GPRO 뱅크' => 'GPRO 뱅크', '국민은행' => '국민은행']" />
                        <x-input label="경비 계좌 번호" size="sm" value="0000 00 0000000" />
                    </div>
                </section>

                @include('partials.hr-record-card', [
                    'title' => '가족 관계', 'actions' => ['excel', 'add', 'save'], 'select' => true, 'rrn' => true,
                    'columns' => ['이름', '관계', '성별', '주민등록번호', '소득 공제', '건강 보험', '장애 여부', '동거여부', '외국인', '비고'],
                ])

                <section class="{{ $card }}">
                    <div class="flex min-w-0 flex-wrap items-center gap-3">
                        <h2 class="{{ $cardTitle }}">병역 사항</h2>
                        <span class="ml-auto flex shrink-0 items-center gap-3">
                            <span class="text-label-1 leading-5 text-mono-black">해당 사항 없음</span>
                            <x-switch size="sm" />
                            <x-button variant="secondary" size="sm" disabled>저장</x-button>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 pt-6 lg:grid-cols-2">
                        <x-dropdown label="역종" size="sm" selected="예비역"
                                    :options="['예비역' => '예비역', '현역' => '현역', '면제' => '면제']" />
                        <x-dropdown label="군별" size="sm" selected="해병"
                                    :options="['해병' => '해병', '육군' => '육군', '해군' => '해군', '공군' => '공군']" />
                        <x-dropdown label="입대일" size="sm" selected="2021.09.01"
                                    :options="['2021.09.01' => '2021.09.01']" />
                        <x-dropdown label="전역일" size="sm" selected="2021.09.01"
                                    :options="['2021.09.01' => '2021.09.01']" />
                        <x-dropdown label="미필 사유" size="sm" selected="해당 사항 없음"
                                    :options="['해당 사항 없음' => '해당 사항 없음']" />
                        <x-input label="병역 특례 여부" size="sm" value="해당 사항 없음" />
                    </div>
                </section>

                @include('partials.hr-record-card', ['title' => '주소 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['국가', '종류', '우편 번호', '주소', '상세 주소']])
                @include('partials.hr-record-card', ['title' => '연락처 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['국가', '종류', '전화 번호', '관계']])
                @include('partials.hr-record-card', ['title' => '장애 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['장애 구분', '장애 유형', '장애 등급', '장애 번호']])
                @include('partials.hr-record-card', ['title' => '보훈 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['보훈 구분', '보훈 번호', '보훈 등급', '보훈 등록일']])
                @include('partials.hr-record-card', ['title' => '어학', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['언어', '구사 수준', '시험 종류', '점수', '등급']])
                @include('partials.hr-record-card', ['title' => '어학 연수', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['연수 국가', '언어', '시작일', '종료일', '연수 목적']])
                @include('partials.hr-record-card', ['title' => '자격증', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['자격증 이름', '발급 기관', '자격증 번호']])
                @include('partials.hr-record-card', ['title' => '봉사 활동', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['기관 이름', '시작 날짜', '종료 날짜', '봉사 내용']])
                @include('partials.hr-record-card', ['title' => '여권 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['여권 번호', '발급 날짜', '만료 날짜', '갱신 날짜']])
                @include('partials.hr-record-card', ['title' => '비자 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['비자 번호', '국가', '비자 구분', '발급일']])
                @include('partials.hr-record-card', ['title' => '보증 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['보증 구분', '보증일', '만료일', '보증 회사', '보증 번호']])
                @include('partials.hr-record-card', ['title' => '증빙 자료', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['증빙 구분', '첨부 파일', '등록 날짜', '최종 등록일']])
                @include('partials.hr-record-card', ['title' => '교육 정보', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['교육 구분', '교육 과정 이름', '일 교육 시간', '총 교육 시간', '교육 기관']])
                @include('partials.hr-record-card', ['title' => '관심 분야', 'actions' => ['add', 'save'], 'select' => true,
                    'columns' => ['분야 이름', '관심 수준', '동호회 이름', 'ID 또는 닉네임']])
            </div>
        </div>
    </x-workspace-shell>
</x-layout>
