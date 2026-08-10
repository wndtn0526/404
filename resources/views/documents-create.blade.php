{{-- 기안 작성 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-113826)
     문서 신청에서 양식을 고르면 오는 화면. 지금은 '지출 결의서 (개인 비용)' 하나만 있다.

     원본 실측(1920) — 본문 1200 (좌 카드 792 + 24 + 우 카드 384)
       뒤로 32 · 제목 30 Bold lh39 · 우상단 버튼 120x36 둘 (사이 16)
       기본 내용 카드 792x292 · 안쪽 30 · 제목 20 Bold lh30 (DS heading-2)
         문서 이름 732x54 · 귀속년월 354x54 · 계좌 정보 354x54 (한 칸에 은행 + 계좌번호)
         '+ 상세 내용 추가' 아이콘 24 + 글자 14
       관련 파일 카드 792x130 · '구글 드라이브' 99x26 우측 · '+ 파일 추가'
       참조 문서 카드 792x130 · '+ 문서 추가'
       결재선 카드 384x156 · '결재선 추가' 우측 · 줄 324x56 (아바타 + 이름/소속 + 역할)
       카드 세로 사이 16

     ⚠️ 원본은 LNB 가 접힌 상태로 그려져 있어 본문이 360 에서 시작한다. 우리는 셸을 그대로
        쓰므로 320 이다. 카드 폭(792 · 384)은 원본 그대로다.
     ⚠️ 원본 제목 앞 이모지는 빼기로 한 규칙대로 넣지 않았다.
     ⚠️ 계좌 정보는 한 칸 안에 은행 드롭다운 + 구분선 + 계좌번호가 들어간다. DS 에 그런
        복합 컨트롤이 없어서 토큰만으로 짰다. 같은 모양이 또 나오면 컴포넌트로 뺀다.
     ⚠️ '구글 드라이브' 버튼은 글자만 넣었다. 원본에 있는 드라이브 로고는 남의 브랜드
        마크라 우리가 그려 넣지 않는다 — 에셋을 받아 오기로 하면 그때 붙인다.
     ⚠️ 계좌 번호는 개인정보다. 지금은 화면뿐이지만 실제로 붙을 때 평문 저장·평문 로그를
        하지 않는다(CLAUDE.md). 저장 전에 담당자 확인부터.
     ⚠️ '임시 저장'·'신청' 엔드포인트가 없다. 붙일 때는 POST + CSRF 로 보내고, 상신은
        문서 상태 전이를 한 곳에서 정의한 뒤 그쪽을 부른다.
     ⚠️ 값은 전부 예시다. --}}
@php
    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
    $card = 'min-w-0 rounded-lg bg-background-normal p-[30px]';
    // '+ ○○ 추가' 줄 — 아이콘 24 + 글자 14, 아직 아무것도 없는 자리라 옅게 나간다
    $addRow = 'inline-flex items-center gap-2 text-label-1 leading-5 text-label-assistive transition-colors hover:text-label-normal focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<x-layout title="지출 결의서 (개인 비용)">
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
        {{-- 셸의 제목 슬롯을 쓰지 않는다. 원본은 뒤로 화살표와 제목이 한 줄이고 오른쪽에
             버튼이 붙는데, 셸 헤더에 넣으면 화살표 자리가 안 나온다. --}}
        <div class="min-w-0" x-data="{ form: { name: '', month: '2021.10', bank: '', account: '' } }">
            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '워크스페이스', 'href' => url('/documents')],
                ['label' => '업무 신청'],
            ]" />

            <div class="flex min-w-0 flex-wrap items-center justify-between gap-4 pt-[30px]">
                <div class="flex min-w-0 items-center gap-2">
                    <a href="{{ url('/documents') }}"
                       class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                       aria-label="문서 신청으로 돌아가기">
                        <x-icon-arrow-left class="size-6" />
                    </a>
                    <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">지출 결의서 (개인 비용)</h1>
                </div>

                {{-- 원본 120x36 — DS 버튼 sm 은 40 이라 한 단계 크다.
                     '신청' 은 원본이 비활성이다. 문서 이름이 차야 누를 수 있게 했다. --}}
                <div class="flex shrink-0 flex-wrap items-center gap-4">
                    <x-button variant="outline" size="sm" class="w-[120px]">임시 저장</x-button>
                    <x-button variant="primary" size="sm" class="w-[120px]"
                              x-bind:disabled="! form.name.trim()">신청</x-button>
                </div>
            </div>

            {{-- 좌 792 + 24 + 우 384 = 1200 --}}
            <div class="mt-[30px] flex min-w-0 flex-col gap-6 pb-10 xl:flex-row xl:items-start">

                <div class="flex min-w-0 flex-1 flex-col gap-4">
                    {{-- ── 기본 내용 ── --}}
                    <section class="{{ $card }}">
                        <h2 class="{{ $cardTitle }}">기본 내용</h2>

                        <div class="pt-[18px]">
                            <x-input label="문서 이름" name="doc_name" size="sm"
                                     placeholder="지출 결의서 (개인 비용)" x-model="form.name" />
                        </div>

                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 pt-6 sm:grid-cols-2">
                            <x-dropdown label="귀속년월" name="doc_month" size="sm"
                                        :options="['2021.10' => '2021.10', '2021.09' => '2021.09', '2021.08' => '2021.08']"
                                        selected="2021.10" x-model="form.month" />

                            {{-- 계좌 정보 — 원본은 한 칸 안에 은행 + 구분선 + 계좌번호다.
                                 DS 에 복합 컨트롤이 없어 토큰만으로 짰다. 높이 32 는 sm 과 같다. --}}
                            <div class="min-w-0">
                                <p class="pb-1.5 text-label-1 font-medium leading-5 text-label-normal">계좌 정보</p>
                                <div class="flex h-8 min-w-0 items-stretch overflow-hidden rounded-md border border-line-solid-normal bg-background-normal focus-within:border-deep-blue-900">
                                    <select name="doc_bank" x-model="form.bank" aria-label="은행 선택"
                                            class="min-w-0 shrink-0 border-0 bg-transparent pl-3 pr-1 text-label-2 text-label-normal focus:outline-none focus:ring-0">
                                        <option value="">은행 선택</option>
                                        @foreach (['국민은행', '신한은행', '하나은행', '우리은행'] as $bank)
                                            <option value="{{ $bank }}">{{ $bank }}</option>
                                        @endforeach
                                    </select>
                                    <span class="my-0 w-px shrink-0 bg-line-solid-normal" aria-hidden="true"></span>
                                    <input type="text" name="doc_account" x-model="form.account"
                                           placeholder="계좌 번호 입력" aria-label="계좌 번호"
                                           class="min-w-0 flex-1 border-0 bg-transparent px-3 text-label-2 text-label-normal caret-deep-blue-900 placeholder:text-label-assistive focus:outline-none focus:ring-0">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="{{ $addRow }} mt-6">
                            <x-icon-square-plus class="size-6 shrink-0" />
                            상세 내용 추가
                        </button>
                    </section>

                    {{-- ── 관련 파일 ── --}}
                    <section class="{{ $card }}">
                        <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                            <h2 class="{{ $cardTitle }}">관련 파일</h2>
                            <x-button variant="outline" size="sm">구글 드라이브</x-button>
                        </div>

                        <button type="button" class="{{ $addRow }} mt-[16px]">
                            <x-icon-square-plus class="size-6 shrink-0" />
                            파일 추가
                        </button>
                    </section>

                    {{-- ── 참조 문서 ── --}}
                    <section class="{{ $card }}">
                        <h2 class="{{ $cardTitle }}">참조 문서</h2>

                        <button type="button" class="{{ $addRow }} mt-[16px]">
                            <x-icon-square-plus class="size-6 shrink-0" />
                            문서 추가
                        </button>
                    </section>
                </div>

                {{-- ── 결재선 384 ── --}}
                <section class="w-full min-w-0 shrink-0 rounded-lg bg-background-normal p-[30px] xl:w-[384px]">
                    <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                        <h2 class="{{ $cardTitle }}">결재선</h2>
                        <button type="button"
                                class="shrink-0 text-label-1 font-medium leading-5 text-label-alternative transition-colors hover:text-label-normal focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                            결재선 추가
                        </button>
                    </div>

                    {{-- 원본 줄 324x56 — 아바타 + 이름/소속 + 오른쪽 역할 --}}
                    <div class="flex min-w-0 items-center gap-2.5 pt-[17px]">
                        <x-thumbnail name="김기안" size="md" shape="circle" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-label-1 font-bold leading-5 text-mono-black">김기안</p>
                            <p class="truncate pt-0.5 text-caption-1 leading-[18px] text-label-alternative">청담원 · 대표</p>
                        </div>
                        <span class="shrink-0 text-label-2 leading-5 text-label-alternative">신청 (본인)</span>
                    </div>
                </section>
            </div>
        </div>
    </x-workspace-shell>
</x-layout>
