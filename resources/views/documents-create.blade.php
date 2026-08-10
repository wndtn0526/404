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
        <div class="min-w-0" x-data="{
                 form: { name: '', month: '2021.10', bank: '', account: '' },

                 // 세 팝업이 채워 넣는 것들. 비었을 땐 '+ 추가' 줄만 보인다.
                 details: [],
                 files: [],
                 refs: [],

                 // 상세 내용 추가 — 원본 안내대로 모두 필수다
                 detail: { project: '', used_at: '2021.12.30', category: '', account: '', amount: '', vendor: '', memo: '' },
                 get detailReady() { return Object.values(this.detail).every((v) => String(v).trim() !== ''); },
                 addDetail() {
                     this.details.push({ ...this.detail });
                     this.detail = { project: '', used_at: '2021.12.30', category: '', account: '', amount: '', vendor: '', memo: '' };
                 },

                 // 결재선 — 진행/열람/참조 세 갈래
                 lineTab: 'progress',
                 picked: [],
                 pick(name, dept) {
                     if (this.picked.some((m) => m.name === name)) return;
                     this.picked.push({ name, dept, role: this.lineTab });
                 },
                 dropPick(name) { this.picked = this.picked.filter((m) => m.name !== name); },
                 tabPicked() { return this.picked.filter((m) => m.role === this.lineTab); },

                 // 참조 문서 — 고를 수 있는 목록을 통째로 들고 있다가 고른 것만 옮긴다
                 catalog: @js(collect($refDocs)->keyBy('no')->all()),
                 checked: [],
                 toggleDoc(no) {
                     this.checked = this.checked.includes(no)
                         ? this.checked.filter((n) => n !== no)
                         : [...this.checked, no];
                 },
                 applyDocs() {
                     for (const no of this.checked) {
                         if (! this.refs.some((r) => r.no === no)) this.refs.push(this.catalog[no]);
                     }
                     this.checked = [];
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

                        {{-- 팝업에서 넣은 상세 내용이 여기 쌓인다 --}}
                        <template x-for="(d, i) in details" :key="i">
                            <div class="mt-4 flex min-w-0 items-start gap-4 rounded-md bg-background-alternative p-4">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-label-1 font-bold leading-5 text-mono-black" x-text="d.memo"></p>
                                    <p class="truncate pt-1 text-caption-1 leading-[18px] text-label-alternative"
                                       x-text="[d.project, d.category, d.used_at].filter(Boolean).join(' · ')"></p>
                                </div>
                                <span class="shrink-0 text-label-1 font-medium leading-5 text-mono-black tabular-nums" x-text="d.amount"></span>
                                <button type="button" @click="details.splice(i, 1)"
                                        class="shrink-0 text-label-alternative transition-colors hover:text-label-normal focus:outline-none"
                                        aria-label="상세 내용 삭제">
                                    <x-icon-close class="size-[18px]" />
                                </button>
                            </div>
                        </template>

                        <button type="button" class="{{ $addRow }} mt-6"
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

                        {{-- 붙은 파일 — 원본은 테두리 없는 줄이다(아이콘 + 이름 + 오른쪽 상태).
                             DS x-file-item 은 테두리 있는 미리보기 카드라 모양이 다르다.
                             같은 줄이 다른 화면에도 나오면 그때 컴포넌트로 뺀다. --}}
                        <template x-for="(f, i) in files" :key="i">
                            <div class="mt-3 flex min-w-0 items-center gap-3">
                                <span class="flex size-6 shrink-0 items-center justify-center rounded-xs bg-deep-blue-900 text-[8px] font-bold text-white"
                                      x-text="f.ext.slice(0, 3)"></span>
                                <p class="min-w-0 flex-1 truncate text-label-1 leading-5 text-mono-black" x-text="f.name"></p>
                                <button type="button" @click="files.splice(i, 1)"
                                        class="shrink-0 text-label-alternative transition-colors hover:text-label-normal focus:outline-none"
                                        aria-label="파일 삭제">
                                    <x-icon-close class="size-[18px]" />
                                </button>
                            </div>
                        </template>

                        {{-- 원본 주석대로 시스템 파일 선택 창을 띄운다 --}}
                        <label class="{{ $addRow }} mt-[16px] cursor-pointer">
                            <x-icon-square-plus class="size-6 shrink-0" />
                            파일 추가
                            <input type="file" multiple class="sr-only"
                                   @change="addFiles($event.target.files); $event.target.value = ''">
                        </label>
                    </section>

                    {{-- ── 참조 문서 ── --}}
                    <section class="{{ $card }}">
                        <h2 class="{{ $cardTitle }}">참조 문서</h2>

                        <template x-for="(r, i) in refs" :key="i">
                            <div class="mt-3 flex min-w-0 items-center gap-3">
                                <x-icon-document class="size-5 shrink-0 text-label-alternative" />
                                <p class="min-w-0 flex-1 truncate text-label-1 leading-5 text-mono-black" x-text="r.title"></p>
                                <span class="shrink-0 text-caption-1 leading-[18px] text-label-alternative tabular-nums" x-text="r.no"></span>
                                <button type="button" @click="refs.splice(i, 1)"
                                        class="shrink-0 text-label-alternative transition-colors hover:text-label-normal focus:outline-none"
                                        aria-label="참조 문서 삭제">
                                    <x-icon-close class="size-[18px]" />
                                </button>
                            </div>
                        </template>

                        <button type="button" class="{{ $addRow }} mt-[16px]"
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
                        <button type="button" @click="$dispatch('open-modal', 'approval-line')"
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

                    {{-- 팝업에서 고른 결재자 --}}
                    <template x-for="(m, i) in picked" :key="m.name">
                        <div class="flex min-w-0 items-center gap-2.5 pt-4">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-deep-blue-800 text-body-2 font-semibold text-white"
                                  x-text="m.name.slice(0, 1)"></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-label-1 font-bold leading-5 text-mono-black" x-text="m.name"></p>
                                <p class="truncate pt-0.5 text-caption-1 leading-[18px] text-label-alternative" x-text="m.dept"></p>
                            </div>
                            <span class="shrink-0 text-label-2 leading-5 text-label-alternative"
                                  x-text="({ progress: '진행', view: '열람', ref: '참조' })[m.role]"></span>
                            <button type="button" @click="dropPick(m.name)"
                                    class="shrink-0 text-label-alternative transition-colors hover:text-label-normal focus:outline-none"
                                    aria-label="결재자 삭제">
                                <x-icon-close class="size-[18px]" />
                            </button>
                        </div>
                    </template>
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
                    <x-table min-width="720px">
                        <x-table.head :columns="[
                            ['label' => '', 'width' => '48px'],
                            ['label' => '문서 유형', 'width' => '90px'],
                            ['label' => '사용 날짜', 'width' => '150px'],
                            ['label' => '상세 내역'],
                            ['label' => '신청자 · 제안자', 'width' => '120px'],
                            ['label' => '문서 번호', 'width' => '150px'],
                        ]" />
                        <tbody>
                            @foreach ($refDocs as $doc)
                                <x-table.row>
                                    <x-table.cell nowrap>
                                        <label class="relative inline-flex cursor-pointer items-center justify-center align-middle">
                                            <input type="checkbox" class="peer sr-only" aria-label="{{ $doc['title'] }} 고르기"
                                                   @change="toggleDoc(@js($doc['no']))">
                                            <span class="size-5 rounded-xs border-[1.5px] border-line-normal-strong bg-background-normal transition-colors peer-checked:border-primary peer-checked:bg-primary"></span>
                                            <x-icon-check class="pointer-events-none absolute h-[90%] w-[90%] text-white opacity-0 transition-opacity peer-checked:opacity-100 [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" />
                                        </label>
                                    </x-table.cell>
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
                   x-bind:class="{ 'hidden': checked.length }">아직 선택한 문서가 없습니다.</p>
                <p class="pt-3 text-label-1 leading-5 text-mono-black"
                   x-bind:class="{ 'hidden': ! checked.length }"
                   x-text="checked.length + '건을 골랐습니다.'"></p>

                <x-slot:footer>
                    <div class="ml-auto flex flex-wrap items-center gap-4">
                        <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">닫기</x-button>
                        <x-button variant="primary" size="sm" class="w-[120px]"
                                  x-bind:disabled="! checked.length"
                                  @click="applyDocs(); open = false">완료</x-button>
                    </div>
                </x-slot:footer>
            </x-modal>
        </div>
    </x-workspace-shell>
</x-layout>
