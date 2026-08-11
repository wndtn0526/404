{{-- 기안 작성 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes)
       빈 화면  node 1002-113826  (/documents/new)
       다 채운 화면 node 1002-115013  (/documents/new-done)
     문서 신청에서 양식을 고르면 오는 화면. 지금은 '지출 결의서 (개인 비용)' 하나만 있다.

     한 화면이 두 상태를 다 낸다. 라우트가 $prefill 을 주면 채워진 채로 열린다 —
     /documents/review-empty 와 같은 방식이다. 빈 상태에서 팝업으로 채워 넣어도 같은 모양이 된다.

     다 채운 화면에서 달라지는 것 (원본 대조)
       상세 내용   '+ 상세 내용 추가' 링크 → '상세 내용' 제목 + 표 (표 맨 아래 줄이 '+ 내역 추가')
       관련 파일   줄 하나가 늘어 카드 130 → 170 · 배지 + 이름 + 오른쪽 초록 체크
       참조 문서   같음
       결재선      '결재선 추가' → '결재선 수정' · 카드 156 → 359 · 진행/열람·참조 사이 구분선

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
     계좌 정보는 원본대로 테두리 하나 안에 은행 + 구분선 + 계좌번호가 든 복합 칸이다.
     그 칸을 x-field-group 으로 새로 만들고, 안쪽에는 DS 드롭다운·입력을 variant="bare" 로
     넣는다 — 테두리는 칸이 그리고 컨트롤은 글자와 캐럿만 낸다.
     ⚠️ 두 칸으로 나눠 쓰고 싶으면 x-field-group 을 빼고 각각 기본 variant 로 두면 된다.
        /styleguide 의 '복합 칸' 절에 두 모양을 나란히 뒀다.
     ⚠️ '구글 드라이브' 버튼은 글자만 넣었다. 원본에 있는 드라이브 로고는 남의 브랜드
        마크라 우리가 그려 넣지 않는다 — 에셋을 받아 오기로 하면 그때 붙인다.
     ⚠️ 계좌 번호는 개인정보다. 지금은 화면뿐이지만 실제로 붙을 때 평문 저장·평문 로그를
        하지 않는다(CLAUDE.md). 저장 전에 담당자 확인부터.
     ⚠️ '임시 저장'·'신청' 엔드포인트가 없다. 붙일 때는 POST + CSRF 로 보내고, 상신은
        문서 상태 전이를 한 곳에서 정의한 뒤 그쪽을 부른다.
     ⚠️ 값은 전부 예시다. --}}
@php
    /*
     * 결재선 팝업의 조직 트리 — 조직 관리 트리와 같은 방식으로 중첩해 적고 평탄화한다.
     * 멤버는 잎이고, 부서는 접었다 편다.
     */
    $orgTree = [
        ['name' => '청담원', 'count' => 100, 'children' => [
            ['name' => '콘텐츠팀', 'count' => 30, 'children' => [
                ['name' => '콘텐츠 1팀', 'count' => 5, 'members' => ['김기안', '이대리', '박사원', '최주임', '정과장']],
                ['name' => '콘텐츠 2팀', 'count' => 5],
                ['name' => '콘텐츠 3팀', 'count' => 10],
            ]],
            ['name' => '운영팀', 'count' => 30],
            ['name' => '개발팀', 'count' => 30],
        ]],
    ];

    // 참조 문서 팝업의 문서 목록
    $refDocs = [];
    foreach ([
        '근태/휴가 신청', '프로그램 구매', '지출 결의서 (개인 비용)',
        '[김기안] 출퇴근 수정 신청', '지출 결의서 (거래처)',
    ] as $i => $title) {
        $refDocs[] = [
            'no' => 'CDW-210801-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
            'kind' => '업무',
            'used_at' => '2021.08.01 03:30',
            'title' => $title,
            'writer' => '김기안',
        ];
    }

    /*
     * 라우트가 준 값이 있으면 그걸로 열고, 없으면 빈 화면이다.
     * 여기서 뽑아 x-data 초기값으로 넘긴다 — 화면을 두 벌 만들지 않기 위해서다.
     */
    $prefill = $prefill ?? [];
    $initForm = $prefill['form'] ?? ['name' => '', 'month' => '2021.10', 'bank' => '', 'account' => ''];
    $initDetails = $prefill['details'] ?? [];
    $initFiles = $prefill['files'] ?? [];
    $initRefs = $prefill['refs'] ?? [];
    $initLine = $prefill['line'] ?? [];

    $cardTitle = 'text-heading-2 font-bold leading-[30px] text-mono-black';
    $card = 'min-w-0 rounded-lg bg-background-normal p-[30px]';
    /*
     * '+ ○○ 추가' 줄 — 아이콘 24 + 글자 14, 아직 아무것도 없는 자리라 옅게 나간다.
     * ⚠️ display 유틸(inline-flex)은 일부러 뺐다. 여기에 박아 두면 hidden 을 걸어도
     *    안 먹는다(CLAUDE.md). 늘 보이는 자리에서는 붙여 쓰고, 접었다 펴는 자리에서는
     *    x-bind:class 로 display 자체를 바꾼다.
     */
    $addRow = 'items-center gap-2 text-label-1 leading-5 text-label-assistive transition-colors hover:text-label-normal focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
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
        <div class="min-w-0" x-data="{
                 form: @js($initForm),

                 // 세 팝업이 채워 넣는 것들. 비었을 땐 '+ 추가' 줄만 보인다.
                 details: @js($initDetails),
                 files: @js($initFiles),
                 refs: @js($initRefs),

                 // 상세 내용 추가 — 원본 안내대로 모두 필수다
                 detail: { project: '', used_at: '2021.12.30', category: '', account: '', amount: '', vendor: '', memo: '' },
                 get detailReady() { return Object.values(this.detail).every((v) => String(v).trim() !== ''); },
                 addDetail() {
                     this.details.push({ ...this.detail });
                     this.detail = { project: '', used_at: '2021.12.30', category: '', account: '', amount: '', vendor: '', memo: '' };
                 },

                 // 결재선 — 진행/열람/참조 세 갈래
                 lineTab: 'progress',
                 picked: @js($initLine),
                 // 원본은 진행(승인)과 열람·참조 사이에 구분선을 하나 넣는다. 성격이 다른 줄이라서다.
                 progressPicked() { return this.picked.filter((m) => m.role === 'progress'); },
                 asidePicked() { return this.picked.filter((m) => m.role !== 'progress'); },
                 byRole(role) { return this.picked.filter((m) => m.role === role); },

                 /*
                  * 열람·참조는 사람이 많아지면 줄로 늘어놓지 않는다 — 원본이 4명까지만 줄이고
                  * 넘으면 아바타를 겹친 한 줄로 접는다(node 1002-115417 · 115425 · 115437).
                  * 진행(승인)은 순서가 뜻을 가지므로 접지 않는다. 원본도 다섯이면 다섯 줄이다.
                  *
                  * ⚠️ 원본 라벨이 '4명 이하일 경우' / '4명 이상일 경우' 라 4 에서 겹친다.
                  *    4명까지는 줄(이하), 넘으면 접는 것으로 봤다.
                  */
                 asideFold: 4,
                 foldsRole(role) { return this.byRole(role).length > this.asideFold; },
                 // 접힌 줄을 누르면 이름 목록이 뜬다. 한 번에 하나만 연다.
                 openRole: null,
                 toggleRole(role) { this.openRole = this.openRole === role ? null : role; },
                 pick(name, dept) {
                     if (this.picked.some((m) => m.name === name)) return;
                     this.picked.push({ name, dept, role: this.lineTab });
                 },
                 dropPick(name) { this.picked = this.picked.filter((m) => m.name !== name); },
                 tabPicked() { return this.picked.filter((m) => m.role === this.lineTab); },

                 /*
                  * 참조 문서 — 고를 수 있는 목록을 통째로 들고 있다가 고른 것만 옮긴다.
                  * selected 라는 이름은 DS 표가 쓰는 것이다. 표 루트에 selectable 을 주지 않고
                  * head·row 에만 주면 표가 자기 스코프를 만들지 않고 이 값을 쓴다 —
                  * 그래야 팝업 푸터의 '완료' 도 같은 값을 본다.
                  * (여기 컴포넌트 태그를 적으면 안 된다 — 속성 안 문자열도 컴파일된다.)
                  */
                 catalog: @js(collect($refDocs)->keyBy('no')->all()),
                 selected: [],
                 applyDocs() {
                     for (const no of this.selected) {
                         if (! this.refs.some((r) => r.no === no)) this.refs.push(this.catalog[no]);
                     }
                     this.selected = [];
                 },

                 /*
                  * '신청' — 원본(node 1002-113762)은 바로 보내지 않고 한 번 물어본다.
                  * 확인을 누르면 문서 신청 목록으로 돌아가면서 스낵바가 뜬다(node 1002-114271).
                  * 다이얼로그와 스낵바는 x-layout 에 심어 둔 x-confirm · x-toast 가 받는다.
                  *
                  * ⚠️ 아직 보낼 곳이 없다. 붙일 때는 POST + CSRF 로 보내고, 상신은 문서 상태 전이를
                  *    한 곳에서 정의한 뒤 그쪽을 부른다. 권한은 화면이 아니라 Policy 에서 본다.
                  *    그때 문구는 서버 세션 플래시로 넘기고 아래 sessionStorage 는 지운다.
                  * ⚠️ 원본 문구의 '내 문서함' 화면은 아직 없다. 생기면 그 화면도 만든다.
                  */
                 askSubmit() {
                     window.dispatchEvent(new CustomEvent('confirm', { detail: {
                         title: '문서를 신청하시겠어요?',
                         message: '신청한 후 이 문서는 “내 문서함”에서 확인이나 수정할 수 있습니다.',
                         cancelLabel: '취소',
                         confirmLabel: '신청',
                         onConfirm: () => {
                             sessionStorage.setItem('cdw.toast', '문서 신청이 완료되었습니다!');
                             window.location.href = '{{ url('/documents') }}';
                         },
                     } }));
                 },

                 /*
                  * 파일 배지 색 — 원본은 사진(deep blue)과 문서(purple) 둘만 정한다.
                  * x-file-badge 의 PHP 쪽 판정과 같은 목록이다. 한쪽만 고치면 어긋난다.
                  */
                 isImage(ext) {
                     return ['JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'HEIC', 'SVG', 'BMP', 'TIF', 'TIFF']
                         .includes(String(ext || '').toUpperCase());
                 },

                 // 파일 — 실제 고른 파일을 이름만 담는다. 올리는 건 아직 없다.
                 addFiles(list) {
                     for (const f of list) this.files.push({ name: f.name, ext: (f.name.split('.').pop() || '').toUpperCase() });
                 },
             }">
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
                              x-bind:disabled="! form.name.trim()" @click="askSubmit()">신청</x-button>
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

                            {{-- 계좌 정보 — 원본대로 테두리 하나 안에 은행 + 구분선 + 계좌번호.
                                 칸은 x-field-group 이 그리고, 안쪽은 DS 컨트롤을 bare 로 넣는다.
                                 컨트롤을 직접 만들지 않으면서 원본 모양을 낸다. --}}
                            <x-field-group label="계좌 정보" size="sm" for="doc_bank">
                                <div class="w-[124px] shrink-0">
                                    <x-dropdown id="doc_bank" name="doc_bank" variant="bare" size="sm"
                                                placeholder="은행 선택"
                                                {{-- 'GPRO 뱅크' 는 원본이 쓰는 가상의 은행이다. 다 채운 화면이
                                                     고르는 값이라 목록에 있어야 빈칸으로 떨어지지 않는다. --}}
                                                :options="['GPRO 뱅크' => 'GPRO 뱅크', '국민은행' => '국민은행', '신한은행' => '신한은행', '하나은행' => '하나은행', '우리은행' => '우리은행']"
                                                :selected="$initForm['bank'] ?? null"
                                                x-model="form.bank" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <x-input name="doc_account" variant="bare" size="sm"
                                             placeholder="계좌 번호 입력" x-model="form.account" />
                                </div>
                            </x-field-group>
                        </div>

                        {{-- 상세 내용이 하나라도 있으면 원본(node 1002-115013)처럼 제목 + 표가 선다.
                             하나도 없으면 아래 '+ 상세 내용 추가' 줄만 남는다(node 1002-113826).

                             원본 실측 — 제목은 계좌 정보 줄에서 40 아래 · 표는 제목에서 20 아래
                               표가 카드 안쪽 폭(792)을 꽉 채운다. 카드 패딩 30 을 음수 여백으로 되돌리고
                               첫 칸만 좌 패딩 30 을 줘서 제목과 세로줄을 맞춘다.
                               열 7개 120/110/120/100/114/100/128 · 머리 56 · 줄 56 · 마지막 줄 28 --}}
                        <template x-if="details.length">
                            <div>
                                <h2 class="{{ $cardTitle }} pt-10">상세 내용</h2>

                                <div class="-mx-[30px] mt-5">
                                    <x-table flush min-width="792px"
                                             class="[&_td:first-child]:pl-[30px] [&_th:first-child]:pl-[30px]">
                                        {{-- 열 이름·차례는 원본 그대로다. 팝업에서 넣을 때 본 이름과
                                             쌓인 뒤 보는 이름이 달라지면 안 된다. --}}
                                        <x-table.head :columns="[
                                            ['label' => '프로젝트', 'width' => '120px'],
                                            ['label' => '비용 분류', 'width' => '110px'],
                                            ['label' => '비용 내역', 'width' => '120px'],
                                            ['label' => '사용 날짜', 'width' => '100px'],
                                            ['label' => '상세 내역', 'width' => '114px'],
                                            ['label' => '거래처 이름', 'width' => '100px'],
                                            ['label' => '사용 금액', 'width' => '128px'],
                                        ]" />
                                        <tbody>
                                            <template x-for="(d, i) in details" :key="i">
                                                <x-table.row class="group/row">
                                                    <x-table.cell tone="muted" nowrap><span x-text="d.project"></span></x-table.cell>
                                                    <x-table.cell tone="muted" nowrap><span x-text="d.category"></span></x-table.cell>
                                                    <x-table.cell tone="muted" nowrap><span x-text="d.account"></span></x-table.cell>
                                                    <x-table.cell tone="muted" nowrap><span class="tabular-nums" x-text="d.used_at"></span></x-table.cell>
                                                    <x-table.cell tone="muted" nowrap><span x-text="d.memo"></span></x-table.cell>
                                                    <x-table.cell tone="muted" nowrap><span x-text="d.vendor"></span></x-table.cell>
                                                    {{-- ⚠️ 원본에는 삭제 자리가 없다(열 7개로 792 가 딱 맞는다). 그렇다고 잘못
                                                         넣은 줄을 지울 방법이 없으면 곤란해서, 열을 늘리지 않고 마지막 칸
                                                         오른쪽에 겹쳐 뒀다. 줄에 마우스를 올리거나 탭으로 짚어야 나온다. --}}
                                                    <x-table.cell tone="muted" nowrap class="relative">
                                                        <span class="tabular-nums" x-text="d.amount"></span>
                                                        <button type="button" @click="details.splice(i, 1)"
                                                                class="absolute right-4 top-1/2 -translate-y-1/2 rounded-xs bg-background-normal p-0.5 text-label-alternative opacity-0 transition group-hover/row:opacity-100 hover:text-label-normal focus:opacity-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                                                aria-label="상세 내용 삭제">
                                                            <x-icon-close class="size-[18px]" />
                                                        </button>
                                                    </x-table.cell>
                                                </x-table.row>
                                            </template>

                                            {{-- 표 맨 아래 28 줄이 '+ 내역 추가' 다. 원본은 잠긴 줄(Row Locked)
                                                 모양이라 글자가 13 Warm gray/400 이다.
                                                 ⚠️ 마지막 줄이라도 아래 선을 남긴다 — 원본에 있다. --}}
                                            <x-table.row :hover="false" class="border-b! border-line-solid-normal!">
                                                <x-table.cell dense colspan="7" class="p-0!">
                                                    <button type="button" @click="$dispatch('open-modal', 'detail-add')"
                                                            class="flex h-7 w-full items-center pl-[30px] pr-4 text-left text-label-2 text-label-assistive transition-colors hover:text-label-normal focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                                                        + 내역 추가
                                                    </button>
                                                </x-table.cell>
                                            </x-table.row>
                                        </tbody>
                                    </x-table>
                                </div>
                            </div>
                        </template>

                        {{-- 아직 아무것도 없을 때만 나오는 줄. 표가 서면 '+ 내역 추가' 가 그 자리를 대신한다. --}}
                        <button type="button" class="{{ $addRow }} mt-6" x-cloak
                                x-bind:class="details.length ? 'hidden' : 'inline-flex'"
                                @click="$dispatch('open-modal', 'detail-add')">
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

                        {{-- 붙은 파일 — 원본(node 1002-115042)은 테두리 없는 줄이다.
                             배지 24 + 이름 14 + 오른쪽 끝 초록 체크(붙었다는 표시).
                             DS x-file-item 은 테두리 있는 미리보기 카드라 모양이 다르다.
                             같은 줄이 다른 화면에도 나오면 그때 컴포넌트로 뺀다.

                             ⚠️ 원본에는 삭제가 없다. 잘못 붙인 파일을 뗄 방법이 없으면 곤란해서
                                줄에 마우스를 올릴 때만 체크 자리에 X 가 뜨게 했다. 가만히 두면 원본 그대로다.
                             ⚠️ 배지 색은 원본이 사진(deep blue)·문서(purple) 둘만 정한다.
                                x-if 로 갈래를 나눈 건 display 유틸이 서로 다투지 않게 하려는 것이다. --}}
                        <template x-for="(f, i) in files" :key="i">
                            <div class="group/file mt-3 flex min-w-0 items-center gap-2">
                                <template x-if="isImage(f.ext)">
                                    <x-file-badge tone="deep-blue" label-expr="f.ext.slice(0, 3)" />
                                </template>
                                <template x-if="! isImage(f.ext)">
                                    <x-file-badge tone="purple" label-expr="f.ext.slice(0, 3)" />
                                </template>

                                <p class="min-w-0 flex-1 truncate text-label-1 leading-5 text-mono-black" x-text="f.name"></p>

                                <span class="relative size-6 shrink-0">
                                    <x-icon-check class="absolute inset-0 size-6 text-status-positive transition-opacity group-hover/file:opacity-0 group-focus-within/file:opacity-0" />
                                    <button type="button" @click="files.splice(i, 1)"
                                            class="absolute inset-0 flex items-center justify-center text-label-alternative opacity-0 transition-opacity hover:text-label-normal focus:opacity-100 focus:outline-none group-hover/file:opacity-100"
                                            aria-label="파일 떼기">
                                        <x-icon-close class="size-[18px]" />
                                    </button>
                                </span>
                            </div>
                        </template>

                        {{-- 원본 주석대로 시스템 파일 선택 창을 띄운다 --}}
                        <label class="{{ $addRow }} mt-[16px] inline-flex cursor-pointer">
                            <x-icon-square-plus class="size-6 shrink-0" />
                            파일 추가
                            <input type="file" multiple class="sr-only"
                                   @change="addFiles($event.target.files); $event.target.value = ''">
                        </label>
                    </section>

                    {{-- ── 참조 문서 ── --}}
                    <section class="{{ $card }}">
                        <h2 class="{{ $cardTitle }}">참조 문서</h2>

                        {{-- 원본(node 1002-115027) — 문서 배지(purple) + 이름 + 오른쪽 끝 초록 체크.
                             문서 번호는 원본에 없다. 뗄 때 어느 문서인지 알아야 해서 남겨 뒀다. --}}
                        <template x-for="(r, i) in refs" :key="i">
                            <div class="group/ref mt-3 flex min-w-0 items-center gap-2">
                                <x-file-badge tone="purple" label="DOC" />
                                <p class="min-w-0 flex-1 truncate text-label-1 leading-5 text-mono-black" x-text="r.title"></p>
                                <span class="shrink-0 text-caption-1 leading-[18px] text-label-alternative tabular-nums" x-text="r.no"></span>

                                <span class="relative size-6 shrink-0">
                                    <x-icon-check class="absolute inset-0 size-6 text-status-positive transition-opacity group-hover/ref:opacity-0 group-focus-within/ref:opacity-0" />
                                    <button type="button" @click="refs.splice(i, 1)"
                                            class="absolute inset-0 flex items-center justify-center text-label-alternative opacity-0 transition-opacity hover:text-label-normal focus:opacity-100 focus:outline-none group-hover/ref:opacity-100"
                                            aria-label="참조 문서 떼기">
                                        <x-icon-close class="size-[18px]" />
                                    </button>
                                </span>
                            </div>
                        </template>

                        <button type="button" class="{{ $addRow }} mt-[16px] inline-flex"
                                @click="$dispatch('open-modal', 'ref-doc-add')">
                            <x-icon-square-plus class="size-6 shrink-0" />
                            문서 추가
                        </button>
                    </section>
                </div>

                {{-- ── 결재선 384 ── --}}
                <section class="w-full min-w-0 shrink-0 rounded-lg bg-background-normal p-[30px] xl:w-[384px]">
                    <div class="flex min-w-0 flex-wrap items-center justify-between gap-3">
                        <h2 class="{{ $cardTitle }}">결재선</h2>
                        {{-- 결재자가 있으면 '수정', 없으면 '추가'. 여는 팝업은 같다. --}}
                        <button type="button" @click="$dispatch('open-modal', 'approval-line')"
                                class="shrink-0 text-label-1 font-medium leading-5 text-label-alternative transition-colors hover:text-label-normal focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                x-text="picked.length ? '결재선 수정' : '결재선 추가'">결재선 추가</button>
                    </div>

                    {{-- 원본 줄은 324x56 이 빈틈없이 쌓인다. 제목에서 17 아래에서 시작한다. --}}
                    <div class="min-w-0 pt-[17px]">
                        {{-- 신청자 — 늘 맨 위다. 지금 로그인한 사람이라 팝업에서 고르는 대상이 아니다.
                             ⚠️ 원본 이름은 '심프로' 지만 이 저장소는 어느 화면에서나 '김기안' 이 로그인한
                                사람이다. 셸(GNB)과 어긋나면 안 돼서 이름만 맞췄다. --}}
                        <div class="flex h-14 min-w-0 items-center gap-2">
                            <x-thumbnail name="김기안" size="md" shape="circle" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">김기안</p>
                                <p class="truncate text-caption-2 leading-[17px] text-label-alternative">청담원 · 대표</p>
                            </div>
                            <span class="shrink-0 text-label-1 leading-5 text-mono-black">신청 (본인)</span>
                        </div>

                        {{-- 진행(승인) — 신청자 바로 아래에 붙는다 --}}
                        <template x-for="m in progressPicked()" :key="'p' + m.name">
                            @include('partials.approval-line-row')
                        </template>

                        {{-- ⚠️ 원본은 진행과 열람·참조 사이에 선을 하나 넣는다(node 1002:115051 ·
                             324x1 Warm gray/100). 결재 흐름을 타는 사람과 보기만 하는 사람을 가르는
                             선이라 한쪽이 비면 긋지 않는다. 원본 여백은 위 17 · 아래 18 이다. --}}
                        <template x-if="progressPicked().length && asidePicked().length">
                            <div class="mt-[17px] mb-[18px] h-px bg-line-solid-neutral"></div>
                        </template>

                        {{-- 열람·참조 — 갈래마다 4명까지는 줄로, 넘으면 아바타 한 줄로 접는다.
                             원본 순서는 열람 먼저 참조 나중이다(node 1002-115048). --}}
                        @foreach (['view' => '열람', 'ref' => '참조'] as $role => $roleLabel)
                            <template x-if="! foldsRole('{{ $role }}')">
                                <div>
                                    <template x-for="m in byRole('{{ $role }}')" :key="'{{ $role }}' + m.name">
                                        @include('partials.approval-line-row')
                                    </template>
                                </div>
                            </template>

                            {{-- 접힌 줄 — 원본 실측: 아바타 그룹 184x40, 오른쪽 끝에 역할 글자.
                                 줄 높이는 펼친 줄과 같은 56 이다(원본 40 + 위아래 8). --}}
                            <template x-if="foldsRole('{{ $role }}')">
                                {{-- ⚠️ click.outside 는 팝오버가 아니라 이 칸에 건다. 팝오버에 걸면
                                     여는 버튼이 팝오버 '밖' 이라 누르는 순간 다시 닫힌다.
                                     자기 갈래가 열려 있을 때만 닫는다 — 다른 갈래를 누르면 그쪽이
                                     막 연 것을 이 핸들러가 도로 닫아 버린다. --}}
                                <div class="relative"
                                     @click.outside="openRole === '{{ $role }}' && (openRole = null)">
                                    <button type="button" @click="toggleRole('{{ $role }}')"
                                            x-bind:aria-expanded="openRole === '{{ $role }}'"
                                            class="flex h-14 w-full min-w-0 items-center gap-2 rounded-md text-left transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                                        <x-avatar-group size="md" overflow="count" :max="4"
                                                        names-expr="byRole('{{ $role }}').map(m => m.name)"
                                                        class="shrink-0" />
                                        <span class="min-w-0 flex-1"></span>
                                        <span class="shrink-0 text-label-1 leading-5 text-mono-black">{{ $roleLabel }}</span>
                                    </button>

                                    {{-- 이름 목록 — 원본 210x336 (node 1002-115544 · 반경 6 · 안쪽 20 ·
                                         아바타 32 + 이름 14 · 줄 간격 44). 원본은 줄에서 오른쪽 167 ·
                                         아래 34 자리에 뜬다. 카드 밖으로 조금 나가는데 원본이 그렇다.
                                         띄운 면이라 여기엔 그림자를 쓴다(CLAUDE.md). --}}
                                    <div x-show="openRole === '{{ $role }}'" x-cloak
                                         @keydown.escape.window="openRole = null"
                                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute left-[167px] top-[34px] z-20 w-[210px] max-w-[calc(100%+23px)] rounded-lg bg-background-normal p-5 shadow-elevation-lg"
                                         role="dialog" aria-label="{{ $roleLabel }} 명단">
                                        <ul class="flex min-w-0 flex-col gap-3">
                                            <template x-for="m in byRole('{{ $role }}')" :key="'p{{ $role }}' + m.name">
                                                <li class="flex min-w-0 items-center gap-2.5">
                                                    <x-thumbnail name-expr="m.name" size="sm" shape="circle" class="shrink-0" />
                                                    <span class="min-w-0 truncate text-label-1 leading-5 text-mono-black" x-text="m.name"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </template>
                        @endforeach
                    </div>
                </section>
            </div>

            {{-- ═══ 상세 내용 추가 ═══ Figma node 1002-113795
                 원본 실측 — 폭 720 · 부제 한 줄 · 칸 두 열 · 상세 내역만 전체 폭
                 원본 안내대로 모두 필수라 하나라도 비면 '추가' 가 안 눌린다. --}}
            <x-modal name="detail-add" title="상세 내용 추가"
                     subtitle="모든 항목이 필수 사항이므로 전부 입력해주세요!"
                     max-width="max-w-[720px]" scroll close-button>
                <div class="grid grid-cols-1 gap-x-[30px] gap-y-6 sm:grid-cols-2">
                    <x-dropdown label="프로젝트" size="sm" placeholder="선택"
                                :options="['요양보호사 개편' => '요양보호사 개편', '방문간호 신규' => '방문간호 신규', '치매전문 보수' => '치매전문 보수']"
                                x-model="detail.project" />
                    <x-input label="사용 날짜" size="sm" x-model="detail.used_at" />

                    <x-dropdown label="비용 분류" size="sm" placeholder="선택"
                                :options="['교육 훈련비' => '교육 훈련비', '교통비' => '교통비', '식대' => '식대', '마케팅비' => '마케팅비']"
                                x-model="detail.category" />
                    <x-dropdown label="비용 내역" size="sm" placeholder="선택"
                                :options="['외부 교육 수강료' => '외부 교육 수강료', '출장 교통비' => '출장 교통비', '식대' => '식대']"
                                x-model="detail.account" />

                    <x-input label="사용 금액" size="sm" placeholder="0 원" x-model="detail.amount" />
                    <x-input label="증빙 거래처 이름" size="sm" placeholder="거래처 이름 입력" x-model="detail.vendor" />

                    <div class="sm:col-span-2">
                        <x-input label="상세 내역" size="sm" placeholder="내용 입력" x-model="detail.memo" />
                    </div>
                </div>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">취소</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]"
                                  x-bind:disabled="! detailReady"
                                  @click="addDetail(); open = false">추가</x-button>
                    </div>
                </x-slot:footer>
            </x-modal>

            {{-- ═══ 결재선 추가 ═══ Figma node 1002-113871
                 원본 실측 — 폭 840 · 좌 검색 + 조직 트리 / 우 탭 셋 + 고른 멤버 · 가운데 세로선
                 안내 두 줄은 원본 문구 그대로다.

                 ⚠️ 원본은 멤버를 오른쪽으로 끌어다 놓을 수도 있다. 끌기는 붙이지 않았다 —
                    이름을 누르면 지금 탭으로 들어간다. 끌기가 필요해지면 그때 더한다. --}}
            <x-modal name="approval-line" title="결재선 추가" max-width="max-w-[840px]" scroll close-button>
                <p class="text-label-2 leading-5 text-label-alternative">· 멤버를 눌러 오른쪽 갈래에 넣습니다.</p>
                <p class="pb-5 text-label-2 leading-5 text-label-alternative">
                    · 결재가 완료되면 열람 멤버를 추가할 수 없기 때문에 이 팝업에서 설정해 주셔야 합니다.
                </p>

                <div class="flex min-w-0 flex-col gap-6 border-t border-line-solid-alternative pt-5 sm:flex-row">
                    {{-- 좌: 조직 트리 --}}
                    <div class="min-w-0 sm:w-[276px] sm:shrink-0 sm:border-r sm:border-line-solid-alternative sm:pr-5">
                        <x-input size="sm" icon="search" placeholder="조직이나 멤버 검색" />

                        <ul class="pt-4">
                            @foreach ($orgTree as $org)
                                <li class="pt-1">
                                    <p class="text-label-1 font-medium leading-5 text-mono-black">{{ $org['name'] }} ({{ $org['count'] }})</p>
                                    <ul class="pl-3">
                                        @foreach ($org['children'] ?? [] as $dept)
                                            <li class="pt-2">
                                                <p class="text-label-1 font-medium leading-5 text-mono-black">{{ $dept['name'] }} ({{ $dept['count'] }})</p>
                                                <ul class="pl-3">
                                                    @foreach ($dept['children'] ?? [] as $team)
                                                        <li class="pt-2">
                                                            <p class="text-label-1 font-medium leading-5 text-mono-black">{{ $team['name'] }} ({{ $team['count'] }})</p>
                                                            @foreach ($team['members'] ?? [] as $member)
                                                                <button type="button"
                                                                        @click="pick(@js($member), @js($org['name'].' · '.$team['name']))"
                                                                        class="mt-2 flex w-full min-w-0 items-center gap-2 pl-3 text-left transition-opacity hover:opacity-60 focus:outline-none focus-visible:underline">
                                                                    <x-thumbnail :name="$member" size="xs" shape="circle" class="shrink-0" />
                                                                    <span class="truncate text-label-1 leading-5 text-mono-black">{{ $member }}</span>
                                                                </button>
                                                            @endforeach
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- 우: 갈래별 목록 --}}
                    <div class="min-w-0 flex-1">
                        <x-tabs name="line_tab" x-model="lineTab"
                                :options="['progress' => '진행 멤버', 'view' => '열람 멤버', 'ref' => '참조 멤버']"
                                selected="progress" accent="strong" />

                        <div class="flex min-w-0 items-center gap-2.5 pt-5">
                            <x-thumbnail name="김기안" size="md" shape="circle" class="shrink-0" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-label-1 font-bold leading-5 text-mono-black">김기안</p>
                                <p class="truncate pt-0.5 text-caption-1 leading-[18px] text-label-alternative">청담원 · 대표</p>
                            </div>
                            <span class="shrink-0 text-label-2 leading-5 text-label-alternative">신청 (본인)</span>
                        </div>

                        <template x-for="m in tabPicked()" :key="m.name">
                            <div class="flex min-w-0 items-center gap-2.5 pt-4">
                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-deep-blue-800 text-body-2 font-semibold text-white"
                                      x-text="m.name.slice(0, 1)"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-label-1 font-bold leading-5 text-mono-black" x-text="m.name"></p>
                                    <p class="truncate pt-0.5 text-caption-1 leading-[18px] text-label-alternative" x-text="m.dept"></p>
                                </div>
                                <button type="button" @click="dropPick(m.name)"
                                        class="shrink-0 text-label-alternative transition-colors hover:text-label-normal focus:outline-none"
                                        aria-label="멤버 빼기">
                                    <x-icon-close class="size-[18px]" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">닫기</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]"
                                  x-bind:disabled="! picked.length" @click="open = false">추가</x-button>
                    </div>
                </x-slot:footer>
            </x-modal>

            {{-- ═══ 문서 추가 ═══ Figma node 1002-115221
                 원본 실측 — 폭 839 · 검색/필터 · '문서 목록' 표 5행 · 페이지네이션
                            구분선 → '선택한 문서' → 닫기/완료

                 ⚠️ 원본 검색·필터는 입력 + '조회' 버튼 + 드롭다운 둘이다. 공용 x-filter-bar
                    로 바꿨다 — 화면마다 필터 생김새가 갈리면 안 된다. --}}
            <x-modal name="ref-doc-add" title="문서 추가" max-width="max-w-[839px]" scroll close-button>
                <x-filter-bar
                    search="문서 이름이나 양식 이름 검색"
                    :active="['kind', 'date']"
                    :columns="[
                        ['key' => 'kind', 'label' => '문서 유형', 'type' => 'select', 'options' => ['업무', '인사', '재무']],
                        ['key' => 'date', 'label' => '날짜', 'type' => 'date'],
                    ]"
                    class="pb-5"
                />

                <h3 class="{{ $cardTitle }}">문서 목록</h3>

                <div class="pt-4">
                    {{-- ⚠️ 표 루트에는 selectable 을 주지 않는다. 주면 표가 자기 x-data 를 만들어
                         푸터의 '완료' 가 그 선택을 못 본다. head·row 에만 주면 바깥 selected 를 쓴다.
                         ⚠️ Blade 주석 안에 컴포넌트 태그를 그대로 적으면 안 된다 — 주석이어도
                            컴파일돼서 짝 없는 여는 태그가 되고 파스 에러가 난다. --}}
                    <x-table min-width="720px">
                        <x-table.head
                            selectable
                            :all-ids="collect($refDocs)->pluck('no')->all()"
                            :columns="[
                                ['label' => '문서 유형', 'width' => '90px'],
                                ['label' => '사용 날짜', 'width' => '150px'],
                                ['label' => '상세 내역'],
                                ['label' => '신청자 · 제안자', 'width' => '120px'],
                                ['label' => '문서 번호', 'width' => '150px'],
                            ]"
                        />
                        <tbody>
                            @foreach ($refDocs as $doc)
                                <x-table.row selectable :value="$doc['no']">
                                    <x-table.cell tone="muted" nowrap>{{ $doc['kind'] }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>
                                        <span class="tabular-nums">{{ $doc['used_at'] }}</span>
                                    </x-table.cell>
                                    <x-table.cell tone="strong">{{ $doc['title'] }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>{{ $doc['writer'] }}</x-table.cell>
                                    <x-table.cell tone="muted" nowrap>
                                        <span class="tabular-nums">{{ $doc['no'] }}</span>
                                    </x-table.cell>
                                </x-table.row>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>

                <div class="pt-5">
                    <x-pagination :total="50" :per-page="10" :current="1" :per-page-options="[10, 50, 100]" />
                </div>

                <h3 class="{{ $cardTitle }} border-t border-line-solid-alternative pt-[30px]">선택한 문서</h3>
                <p class="pt-3 text-label-1 leading-5 text-label-assistive"
                   x-bind:class="{ 'hidden': selected.length }">아직 선택한 문서가 없습니다.</p>
                <p class="pt-3 text-label-1 leading-5 text-mono-black"
                   x-bind:class="{ 'hidden': ! selected.length }"
                   x-text="selected.length + '건을 골랐습니다.'"></p>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">닫기</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]"
                                  x-bind:disabled="! selected.length"
                                  @click="applyDocs(); open = false">완료</x-button>
                    </div>
                </x-slot:footer>
            </x-modal>
        </div>
    </x-workspace-shell>
</x-layout>
