@php
    /* 개발용 살아있는 스타일가이드. 컴포넌트를 실제로 렌더하므로 토큰이나 컴포넌트가 깨지면
       여기서 바로 드러난다. 새 컴포넌트를 만들면 여기에도 추가한다.

       ⚠️ 아래 배열은 토큰 이름이 아니라 "완성된 유틸리티 클래스명"을 담는다.
          Tailwind 는 파일 내용을 문자열로 훑어 클래스를 찾기 때문에, bg-{$token} 처럼
          런타임에 조립하면 CSS 가 생성되지 않아 스와치가 투명하게 나온다. */

    $sections = [
        'colors' => '색 · 토큰',
        'typography' => '타이포',
        'buttons' => '버튼',
        'badges' => '배지',
        'forms' => '폼 컨트롤',
        'table' => '표',
        'nav' => '탭 · 페이지네이션',
        'surfaces' => '면 · 오버레이',
        'navigation' => '내비게이션',
        'icons' => '아이콘',
    ];

    // 원본 LNB 예시 데이터
    $lnbItems = [
        ['label' => '결재함', 'href' => '#', 'icon' => 'inbox', 'active' => true, 'badge' => 3],
        ['label' => '기안 문서', 'href' => '#', 'icon' => 'document-search'],
        ['label' => '문서함', 'href' => '#', 'icon' => 'list'],
        ['label' => '설정', 'href' => '#', 'icon' => 'setting'],
    ];

    $brand = [
        'bg-primary' => 'Primary / Normal',
        'bg-primary-strong' => 'Primary / Strong',
        'bg-primary-heavy' => 'Primary / Heavy',
        'bg-primary-surface' => 'Primary / Surface',
        'bg-primary-soft' => 'Primary / Soft',
        'bg-inverse-primary' => 'Inverse / Primary',
    ];

    $labelClasses = [
        'text-label-strong',
        'text-label-normal',
        'text-label-neutral',
        'text-label-alternative',
        'text-label-assistive',
        'text-label-disable',
    ];

    $surfaceClasses = [
        'bg-background-normal',
        'bg-background-alternative',
        'bg-fill-alternative',
        'bg-fill-normal',
        'bg-fill-strong',
        'bg-interaction-disable',
    ];

    $lineClasses = [
        'border-line-solid-alternative',
        'border-line-solid-neutral',
        'border-line-solid-normal',
        'border-line-normal-neutral',
        'border-line-normal-normal',
        'border-line-normal-strong',
    ];

    $statusClasses = ['bg-status-positive', 'bg-status-cautionary', 'bg-status-negative'];

    $accentClasses = [
        'bg-accent-fg-red',
        'bg-accent-fg-orange',
        'bg-accent-fg-lime',
        'bg-accent-fg-green',
        'bg-accent-fg-cyan',
        'bg-accent-fg-blue',
        'bg-accent-fg-violet',
        'bg-accent-fg-purple',
        'bg-accent-fg-pink',
    ];

    $shadowClasses = [
        'shadow-elevation-xs',
        'shadow-elevation-sm',
        'shadow-elevation-md',
        'shadow-elevation-lg',
        'shadow-elevation-xl',
        'shadow-spread-small',
    ];

    $typeScale = [
        'text-display-1' => '48 · 전자결재에선 거의 안 씀',
        'text-display-2' => '40',
        'text-display-3' => '36',
        'text-title-1' => '32 · 화면 제목',
        'text-title-2' => '30',
        'text-title-3' => '24 · 섹션 제목',
        'text-heading-1' => '22',
        'text-heading-2' => '20 · 카드 제목',
        'text-headline-1' => '19',
        'text-headline-2' => '18',
        'text-body-1' => '16 · 폼 입력 기본',
        'text-body-2' => '15 · 표 본문 · 버튼',
        'text-label-1' => '14 · 보조 라벨',
        'text-label-2' => '13 · 메타 · 힌트',
        'text-caption-1' => '12 · 타임스탬프',
        'text-caption-2' => '11',
    ];

    $iconNames = collect(glob(resource_path('svg/icons/*.svg')))
        ->map(fn ($p) => pathinfo($p, PATHINFO_FILENAME))
        ->sort()
        ->values();

    $docs = [
        ['no' => 'EA-2026-0142', 'title' => '노트북 4대 구매 요청', 'writer' => '김기안', 'status' => '결재 대기', 'tone' => 'orange', 'at' => '2026-07-31'],
        ['no' => 'EA-2026-0141', 'title' => '7월 방문간호 차량 유지비 정산', 'writer' => '이대리', 'status' => '진행 중', 'tone' => 'blue', 'at' => '2026-07-30'],
        ['no' => 'EA-2026-0140', 'title' => '재직증명서 발급 신청', 'writer' => '박사원', 'status' => '승인', 'tone' => 'green', 'at' => '2026-07-30'],
        ['no' => 'EA-2026-0139', 'title' => '외부 교육 참가비 지원', 'writer' => '최주임', 'status' => '반려', 'tone' => 'red', 'at' => '2026-07-29'],
        ['no' => 'EA-2026-0138', 'title' => '사무용품 정기 발주', 'writer' => '정과장', 'status' => '완료', 'tone' => 'green', 'at' => '2026-07-28'],
    ];
@endphp

<x-layout title="디자인 시스템" :bare-title="true">
    <div class="mx-auto flex max-w-[1400px] gap-10 px-6 py-10 lg:px-10">
        {{-- 사이드 목차 --}}
        <nav class="sticky top-10 hidden h-fit w-44 shrink-0 flex-col gap-0.5 lg:flex">
            <p class="mb-2 text-caption-1 font-semibold uppercase tracking-wide text-label-assistive">Contents</p>
            @foreach ($sections as $id => $label)
                <a href="#{{ $id }}"
                   class="rounded-md px-3 py-1.5 text-label-1 text-label-alternative transition-colors hover:bg-fill-normal hover:text-label-normal">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <main class="flex min-w-0 flex-1 flex-col gap-16">
            {{-- ═══ 헤더 ═══ --}}
            <header class="flex flex-col gap-3">
                <div><x-badge color="primary" size="md">Design System</x-badge></div>
                <h1 class="text-title-1 font-bold text-label-normal">디자인 시스템</h1>
                <p class="text-body-1 text-label-alternative">
                    디자인 시스템 토큰 기반 · Blade 컴포넌트 · 아이콘 {{ $iconNames->count() }}종
                </p>
                <div class="rounded-md border border-line-solid-normal bg-primary-soft px-4 py-3">
                    <p class="text-label-1 text-label-neutral">
                        <strong class="font-semibold text-label-normal">값의 출처는 Figma 디자인 가이드 다.</strong>
                        토큰을 고칠 일이 생기면 Figma → 추출본
                        <code class="rounded-md bg-fill-normal px-1 font-mono text-label-2">resources/design/design-tokens.json</code> → 이 저장소
                        <code class="rounded-md bg-fill-normal px-1 font-mono text-label-2">resources/css/tokens.css</code> 를 함께 맞춘다.
                        뷰에 raw hex 를 쓰지 않는다.
                    </p>
                    <p class="mt-2 text-label-2 text-label-alternative">
                        <code class="font-mono">[파생]</code> 표시가 붙은 토큰은 원본에 대응 단계가 없어 계산해 채운 값이다.
                        Figma 에 해당 단계가 생기면 교체한다.
                    </p>
                </div>
            </header>

            {{-- ═══ 색 ═══ --}}
            <section id="colors" class="flex flex-col gap-6">
                <h2 class="text-title-3 font-bold text-label-normal">색 · 토큰</h2>

                <div>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Brand (Black)</h3>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($brand as $cls => $label)
                            <div class="flex flex-col gap-1.5">
                                <div class="h-20 rounded-md border border-line-solid-neutral {{ $cls }}"></div>
                                <p class="text-label-2 font-medium text-label-normal">{{ $label }}</p>
                                <code class="font-mono text-caption-1 text-label-assistive">{{ $cls }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Label (텍스트)</h3>
                    <div class="flex flex-col gap-1 rounded-md border border-line-solid-neutral bg-background-normal p-5">
                        @foreach ($labelClasses as $cls)
                            <div class="flex items-baseline justify-between gap-4 py-1">
                                <span class="text-body-1 {{ $cls }}">가나다 Approval 0123</span>
                                <code class="shrink-0 font-mono text-caption-1 text-label-assistive">{{ $cls }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Background · Fill</h3>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ($surfaceClasses as $cls)
                                <div class="flex flex-col gap-1.5">
                                    <div class="h-16 rounded-md border border-line-solid-normal {{ $cls }}"></div>
                                    <code class="break-all font-mono text-caption-2 text-label-assistive">{{ $cls }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Line (보더)</h3>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach ($lineClasses as $cls)
                                <div class="flex flex-col gap-1.5">
                                    <div class="h-16 rounded-md border-2 bg-background-normal {{ $cls }}"></div>
                                    <code class="break-all font-mono text-caption-2 text-label-assistive">{{ $cls }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Status · Accent</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($statusClasses as $cls)
                            <div class="flex items-center gap-2 rounded-md border border-line-solid-neutral bg-background-normal px-3 py-2">
                                <span class="size-4 rounded-full {{ $cls }}"></span>
                                <code class="font-mono text-caption-1 text-label-alternative">{{ $cls }}</code>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex flex-wrap gap-3">
                        @foreach ($accentClasses as $cls)
                            <div class="flex items-center gap-2 rounded-md border border-line-solid-neutral bg-background-normal px-3 py-2">
                                <span class="size-4 rounded-full {{ $cls }}"></span>
                                <code class="font-mono text-caption-2 text-label-alternative">{{ str_replace('bg-accent-fg-', '', $cls) }}</code>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-3 text-label-1 text-label-alternative">
                        Accent 는 <code class="font-mono text-label-2">fg-</code> 계열(텍스트·아이콘용, 명도 대비 확보)을 쓴다.
                        면을 채울 때만 <code class="font-mono text-label-2">bg-accent-*</code>.
                    </p>
                </div>

                <div>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Elevation</h3>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($shadowClasses as $cls)
                            <div class="flex flex-col items-center gap-2">
                                <div class="size-20 rounded-md bg-background-normal {{ $cls }}"></div>
                                <code class="font-mono text-caption-2 text-label-assistive">{{ $cls }}</code>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ═══ 타이포 ═══ --}}
            <section id="typography" class="flex flex-col gap-4">
                <div>
                    <h2 class="text-title-3 font-bold text-label-normal">타이포</h2>
                    <p class="mt-1 text-body-2 text-label-alternative">
                        Pretendard. <code class="font-mono text-label-2">text-{토큰}</code> 이 크기·행간·자간을 한 번에 적용한다.
                        색·굵기는 역할에 따라 따로 지정.
                    </p>
                </div>
                <div class="flex flex-col divide-y divide-line-solid-alternative rounded-md border border-line-solid-neutral bg-background-normal">
                    @foreach ($typeScale as $cls => $note)
                        <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-1 px-5 py-3">
                            <span class="{{ $cls }} font-semibold text-label-normal">결재 문서를 상신합니다</span>
                            <span class="flex shrink-0 items-baseline gap-3">
                                <span class="text-caption-1 text-label-assistive">{{ $note }}</span>
                                <code class="font-mono text-caption-1 text-label-alternative">{{ $cls }}</code>
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ═══ 버튼 ═══ --}}
            <section id="buttons" class="flex flex-col gap-5">
                <h2 class="text-title-3 font-bold text-label-normal">버튼</h2>

                <x-card>
                    <p class="mb-3 text-label-1 font-semibold text-label-alternative">variant</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-button variant="primary">primary</x-button>
                        <x-button variant="secondary">secondary</x-button>
                        <x-button variant="subtle">subtle</x-button>
                        <x-button variant="outline">outline</x-button>
                        <x-button variant="ghost">ghost</x-button>
                        <x-button variant="danger">danger</x-button>
                    </div>
                    <p class="mt-4 text-label-1 text-label-alternative">
                        <code class="font-mono text-label-2">danger</code> 는 원본에 없고 전자결재에서 추가했다.
                        반려·삭제를 primary 로 내보내면 승인과 구분이 안 된다.
                    </p>
                </x-card>

                <div class="grid gap-5 lg:grid-cols-2">
                    <x-card>
                        <p class="mb-3 text-label-1 font-semibold text-label-alternative">size</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-button size="sm">sm · 표 행</x-button>
                            <x-button size="md">md · 기본</x-button>
                            <x-button size="lg">lg</x-button>
                        </div>
                    </x-card>
                    <x-card>
                        <p class="mb-3 text-label-1 font-semibold text-label-alternative">아이콘 · 비활성</p>
                        <div class="flex flex-wrap items-center gap-3">
                            <x-button icon="check">승인</x-button>
                            <x-button variant="danger" icon="close">반려</x-button>
                            <x-button variant="outline" icon-trailing="chevron-right">다음</x-button>
                            <x-button disabled>비활성</x-button>
                        </div>
                    </x-card>
                </div>

                <x-card>
                    <p class="mb-3 text-label-1 font-semibold text-label-alternative">실제 조합 — 문서 상세 액션 바</p>
                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-line-solid-alternative pt-4">
                        <x-button variant="ghost">목록</x-button>
                        <x-button variant="outline" icon="download">PDF</x-button>
                        <x-button variant="danger" icon="close">반려</x-button>
                        <x-button icon="check">승인</x-button>
                    </div>
                </x-card>
            </section>

            {{-- ═══ 배지 ═══ --}}
            <section id="badges" class="flex flex-col gap-5">
                <h2 class="text-title-3 font-bold text-label-normal">배지</h2>
                <x-card>
                    <div class="flex flex-col gap-4">
                        @foreach (['solid', 'outlined', 'filled'] as $variant)
                            <div class="flex flex-wrap items-center gap-2">
                                <code class="w-20 shrink-0 font-mono text-caption-1 text-label-assistive">{{ $variant }}</code>
                                @foreach (['neutral', 'primary', 'blue', 'green', 'red', 'cyan', 'orange', 'violet'] as $color)
                                    <x-badge :variant="$variant" :color="$color" size="md">{{ $color }}</x-badge>
                                @endforeach
                            </div>
                        @endforeach
                        <div class="flex flex-wrap items-center gap-2 border-t border-line-solid-alternative pt-4">
                            <code class="w-20 shrink-0 font-mono text-caption-1 text-label-assistive">size</code>
                            @foreach (['xs', 'sm', 'md', 'lg'] as $size)
                                <x-badge :size="$size" color="primary">{{ $size }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                </x-card>
            </section>

            {{-- ═══ 폼 ═══ --}}
            <section id="forms" class="flex flex-col gap-5">
                <h2 class="text-title-3 font-bold text-label-normal">폼 컨트롤</h2>

                {{-- ── 원본 Input 원본 (Figma 1002:518593) ─────────────────────────
                     피그마 목차 그대로: 6개 변형 × 상태. 규칙도 원본 주석에서 옮겼다. --}}
                <x-card>
                    <div class="mb-5">
                        <p class="text-headline-2 font-semibold text-label-normal">원본 Input — 원본 6종</p>
                        <p class="mt-1 text-label-1 text-label-alternative">
                            Figma 디자인 가이드 Input (1002:518593). 박스 높이에 따라 <strong>Box 40 / Box 32</strong>로 나뉜다.
                            인풋 박스는 <strong>안쪽 좌우 패딩이 고정</strong>이고 양옆으로만 늘어난다.
                            Multi Line 을 제외한 나머지는 <strong>높이가 고정</strong>이다. 그림자는 없다.
                        </p>
                    </div>

                    {{-- Status 매트릭스 — 호버·액티브는 실제로 만질 수 없으니 고정 상태로 함께 보여준다 --}}
                    <div class="mb-6 overflow-x-auto">
                        <p class="mb-3 text-label-1 font-semibold text-label-alternative">Status 6종 (Box 40 기준)</p>
                        <div class="grid min-w-[720px] grid-cols-6 gap-3">
                            @foreach ([
                                'Default' => 'border-line-solid-normal',
                                'Hover' => 'border-line-solid-normal bg-background-elevated-alternative',
                                'Active' => 'border-deep-blue-900',
                                'Success' => 'border-line-solid-normal',
                                'Error' => 'border-status-negative',
                                'Disabled' => 'border-interaction-disable bg-interaction-disable',
                            ] as $status => $cls)
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-caption-1 font-semibold text-label-alternative">{{ $status }}</span>
                                    <div class="flex h-10 items-center gap-px rounded-md border px-[11px] text-label-1 {{ $cls }} {{ $status === 'Disabled' ? 'text-label-disable' : 'text-label-strong' }}">
                                        {{ $status === 'Disabled' ? 'Filled' : 'Typing' }}
                                        @if ($status === 'Active')
                                            {{-- 원본 cursor bar — 1×17px deep blue --}}
                                            <span class="ml-px block h-[17px] w-px bg-deep-blue-900"></span>
                                        @endif
                                    </div>
                                    @if ($status === 'Success')
                                        <span class="text-label-2 text-status-positive">사용할 수 있습니다</span>
                                    @elseif ($status === 'Error')
                                        <span class="text-label-2 text-status-negative">이미 사용 중입니다</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-3 text-label-2 text-label-alternative">
                            ⚠️ <strong>Active 는 브랜드색(검정)이 아니라 <code class="font-mono">deep-blue-900</code></strong> 이다 — 원본을 그대로 따랐다.
                            <strong>보더 색만 바뀐다 — 링도 그림자도 없다.</strong> 캐럿(1×17px)도 같은 파랑이다.
                            Success 는 보더가 바뀌지 않고 하단 메시지만 초록이다. 박스 안쪽 좌우 패딩은 11px 고정.
                        </p>
                    </div>

                    {{-- Version 4종 × 크기 --}}
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="flex flex-col gap-3">
                            <p class="text-label-1 font-semibold text-label-alternative">1. Input Box 40 — Placeholder / Filled</p>
                            <x-input size="md" placeholder="Placeholder" />
                            <x-input size="md" value="Filled" />
                            <x-input size="md" value="Filled" icon="search" />
                            <x-input size="md" value="Filled" disabled />
                        </div>

                        <div class="flex flex-col gap-3">
                            <p class="text-label-1 font-semibold text-label-alternative">2. Input Box 32 — Placeholder / Filled</p>
                            <x-input size="sm" placeholder="Placeholder" />
                            <x-input size="sm" value="Filled" />
                            <x-input size="sm" value="Filled" icon="search" />
                            <x-input size="sm" value="Filled" disabled />
                        </div>

                        <div class="flex flex-col gap-3">
                            <p class="text-label-1 font-semibold text-label-alternative">3. Input Box Multi Line <span class="font-normal">— 유일하게 높이가 늘어난다</span></p>
                            <x-textarea placeholder="Placeholder" rows="3" />
                            <x-textarea rows="3" error="내용을 입력해 주세요">Typing</x-textarea>
                        </div>

                        <div class="flex flex-col gap-3">
                            <p class="text-label-1 font-semibold text-label-alternative">6. Line Input</p>
                            <x-input variant="line" size="md" placeholder="Placeholder" />
                            <x-input variant="line" size="md" value="Filled" />
                            <x-input variant="line" size="md" value="Typing" error="확인이 필요합니다" />
                            <x-input variant="line" size="md" value="Filled" disabled />
                        </div>

                        <div class="flex flex-col gap-3">
                            <p class="text-label-1 font-semibold text-label-alternative">4. Input Box 40 Tag <span class="font-normal">— Default·Hover·Disabled 만 정의됨</span></p>
                            <x-input size="md" :tags="['기획팀', '김기안']" placeholder="이름 입력" />
                        </div>

                        <div class="flex flex-col gap-3">
                            <p class="text-label-1 font-semibold text-label-alternative">5. Input Box 32 Tag</p>
                            <x-input size="sm" :tags="['긴급']" placeholder="태그 입력" />
                        </div>
                    </div>
                </x-card>

                <div class="grid gap-5 lg:grid-cols-2">
                    <x-card>
                        <p class="mb-4 text-label-1 font-semibold text-label-alternative">입력 — 상태</p>
                        <div class="flex flex-col gap-4">
                            <x-input label="문서 제목" name="sg_title" placeholder="예) 노트북 4대 구매 요청" required />
                            <x-input label="검색" name="sg_search" icon="search" placeholder="문서번호 · 제목 · 기안자" clearable />
                            <x-input label="금액" name="sg_amount" hint="숫자만 입력합니다." placeholder="0" />
                            <x-input label="반려 사유" name="sg_err" error="반려 사유는 필수입니다." />
                            <x-input label="비활성" name="sg_dis" value="수정할 수 없는 값" disabled />
                        </div>
                    </x-card>

                    <x-card>
                        <p class="mb-4 text-label-1 font-semibold text-label-alternative">선택 · 날짜 · 여러 줄</p>
                        <div class="flex flex-col gap-4">
                            <x-dropdown
                                label="문서 종류"
                                name="sg_type"
                                :options="['purchase' => '구매 요청', 'expense' => '지출 결의', 'leave' => '휴가 신청', 'cert' => '증명서 발급']"
                                selected="purchase"
                            />
                            <x-datepicker label="기안일" name="sg_date" />
                            <x-textarea label="상신 의견" name="sg_memo" rows="3" :maxlength="200" placeholder="결재자에게 전달할 내용을 적습니다." />
                        </div>
                    </x-card>
                </div>

                <x-card>
                    <p class="mb-4 text-label-1 font-semibold text-label-alternative">체크 · 스위치 · 칩</p>
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                        <x-checkbox label="긴급 문서로 상신" name="sg_urgent" />
                        <x-checkbox label="처리 결과 메일 수신" name="sg_mail" checked />
                        <x-switch label="결재 알림" name="sg_noti" checked />
                        <x-switch label="대결 허용" name="sg_deputy" size="sm" />
                    </div>
                    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-line-solid-alternative pt-4">
                        <x-chip active>전체</x-chip>
                        <x-chip>결재 대기</x-chip>
                        <x-chip>진행 중</x-chip>
                        <x-chip>완료</x-chip>
                    </div>
                </x-card>

                <x-card>
                    <p class="mb-4 text-label-1 font-semibold text-label-alternative">첨부파일</p>
                    <div class="flex flex-col gap-3">
                        <x-file-dropzone name="sg_files" />
                        <x-file-item name="견적서_3사비교.pdf" size="482 KB" />
                    </div>
                    <p class="mt-4 text-label-1 text-label-alternative">
                        첨부는 공개 디스크에 두지 않는다. 권한 확인 후 스트리밍한다.
                    </p>
                </x-card>
            </section>

            {{-- ═══ 표 ═══ --}}
            <section id="table" class="flex flex-col gap-5">
                <h2 class="text-title-3 font-bold text-label-normal">표</h2>
                <x-table selectable min-width="820px">
                    <x-table.head
                        selectable
                        :all-ids="collect($docs)->pluck('no')->all()"
                        :columns="[
                            ['label' => '문서번호', 'width' => '150px'],
                            ['label' => '제목'],
                            ['label' => '기안자', 'width' => '100px'],
                            ['label' => '상태', 'align' => 'center', 'width' => '130px'],
                            ['label' => '기안일', 'align' => 'right', 'width' => '110px'],
                        ]"
                    />
                    <tbody>
                        @foreach ($docs as $doc)
                            <x-table.row selectable :value="$doc['no']">
                                <x-table.cell tone="muted" nowrap>
                                    <code class="font-mono text-label-2">{{ $doc['no'] }}</code>
                                </x-table.cell>
                                <x-table.cell tone="strong">{{ $doc['title'] }}</x-table.cell>
                                <x-table.cell tone="muted">{{ $doc['writer'] }}</x-table.cell>
                                <x-table.cell align="center">
                                    <x-badge :color="$doc['tone']" size="sm">{{ $doc['status'] }}</x-badge>
                                </x-table.cell>
                                <x-table.cell align="right" tone="muted" nowrap>{{ $doc['at'] }}</x-table.cell>
                            </x-table.row>
                        @endforeach
                    </tbody>
                </x-table>
            </section>

            {{-- ═══ 내비 ═══ --}}
            <section id="nav" class="flex flex-col gap-5">
                <h2 class="text-title-3 font-bold text-label-normal">탭 · 페이지네이션</h2>
                <x-card>
                    <x-tabs
                        name="sg_tabs"
                        :options="['inbox' => '결재 대기', 'mine' => '내 기안', 'done' => '완료', 'all' => '전체']"
                        selected="inbox"
                        accent="primary"
                    />
                    <div class="mt-6">
                        <x-segmented name="sg_seg" :options="['list' => '목록', 'board' => '보드']" selected="list" />
                    </div>
                </x-card>
                <x-card padding="sm">
                    <x-pagination :total="142" :per-page="10" :current="3" :per-page-options="[10, 50, 100]" />
                </x-card>
            </section>

            {{-- ═══ 면 ═══ --}}
            <section id="surfaces" class="flex flex-col gap-5">
                <h2 class="text-title-3 font-bold text-label-normal">면 · 오버레이</h2>

                <div class="grid gap-5 sm:grid-cols-3">
                    <x-stat-tile label="결재 대기" value="12" unit="건" sub="어제보다 +3" />
                    <x-stat-tile label="이번 달 기안" value="48" unit="건" note="반려 2건 포함" />
                    <x-stat-tile label="평균 결재 소요" value="1.4" unit="일" sub="목표 2일 이내" sub-tone="positive" />
                </div>

                <div class="grid gap-5 sm:grid-cols-4">
                    <x-card elevation="none" padding="sm">
                        <p class="text-label-1 font-semibold text-label-normal">elevation=none</p>
                    </x-card>
                    <x-card elevation="xs" padding="sm">
                        <p class="text-label-1 font-semibold text-label-normal">xs</p>
                    </x-card>
                    <x-card elevation="sm" padding="sm">
                        <p class="text-label-1 font-semibold text-label-normal">sm</p>
                    </x-card>
                    <x-card elevation="md" padding="sm">
                        <p class="text-label-1 font-semibold text-label-normal">md</p>
                    </x-card>
                </div>

                <x-card>
                    <p class="mb-3 text-label-1 font-semibold text-label-alternative">모달</p>
                    <div x-data>
                        <x-button variant="outline" x-on:click="$dispatch('open-modal', 'sg-approve')">
                            승인 확인 모달 열기
                        </x-button>
                    </div>
                </x-card>

                <x-modal name="sg-approve" title="이 문서를 승인하시겠습니까?" subtitle="EA-2026-0142 · 노트북 4대 구매 요청" close-button>
                    <x-textarea label="승인 의견 (선택)" name="sg_approve_memo" rows="3" size="md" />

                    <x-slot:footer>
                        <x-button variant="outline" block x-on:click="open = false">취소</x-button>
                        <x-button block icon="check" x-on:click="open = false">승인</x-button>
                    </x-slot:footer>
                </x-modal>
            </section>

            {{-- ═══ 내비게이션 (원본 GNB · LNB · Breadcrumb · Tooltip · Thumbnail) ═══ --}}
            <section id="navigation" class="flex flex-col gap-6">
                <div>
                    <h2 class="text-title-3 font-bold text-label-normal">내비게이션</h2>
                    <p class="mt-1 text-body-2 text-label-alternative">
                        원본에 있는데 이 저장소에 없던 것들. Figma Dev Mode 로 실측해 옮겼다.
                    </p>
                </div>

                <x-card>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">워크스페이스 셸</h3>
                    <p class="text-label-1 text-label-alternative">
                        <code class="font-mono text-label-2">&lt;x-workspace-shell&gt;</code> — 워크스페이스 레일(54px)이 붙은
                        다크 LNB + 배경 투명 GNB. 출처는 <strong class="font-semibold text-label-normal">디자인 가이드가 아니라</strong>
                        워크스페이스 화면 파일(node 1-299)이다.
                    </p>
                    <p class="mt-2 text-label-1 text-label-alternative">
                        아래 GNB·LNB 와 <strong class="font-semibold text-label-normal">역할이 겹친다.</strong>
                        화면 단위로 하나만 고른다 — 섞으면 헤더가 두 겹이 된다.
                    </p>
                    <p class="mt-3 text-label-1 text-label-alternative">
                        전체 화면을 차지해서 여기에 끼워 넣으면 레이아웃이 깨진다. 실물은
                        <a href="{{ route('contents') }}" class="font-semibold text-primary underline">/contents</a> 에서 본다.
                    </p>
                </x-card>

                <x-card>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">GNB · 글로벌 헤더</h3>
                    <div class="overflow-hidden rounded-md border border-line-solid-neutral">
                        <x-gnb title="전자결재" user="김기안" :hasAlarm="true" class="!static" />
                    </div>
                    <p class="mt-3 text-label-1 text-label-alternative">
                        높이 56px. 메뉴 버튼은 <code class="font-mono text-label-2">lnb-toggle</code> 이벤트를 쏘고 LNB 가 받는다.
                    </p>
                </x-card>

                <x-card>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">LNB · 사이드바</h3>
                    <div class="overflow-hidden rounded-md border border-line-solid-neutral">
                        <x-lnb :items="$lnbItems" heading="전자결재" class="!flex h-80" />
                    </div>
                    <p class="mt-3 text-label-1 text-label-alternative">
                        너비 240px · 항목 높이 32px · 반경 3px. 어두운 면(Side Bar BG 01/02)을 쓴다.
                    </p>
                </x-card>

                <x-card>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Breadcrumb</h3>
                    <x-breadcrumb :items="[
                        ['label' => '홈', 'href' => '#'],
                        ['label' => '결재함', 'href' => '#'],
                        ['label' => '2026년 3분기 예산 신청'],
                    ]" />
                </x-card>

                <x-card>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Tooltip</h3>
                    <div class="flex flex-wrap items-center gap-6 py-4">
                        <x-tooltip text="기안자에게 문서가 돌아갑니다" position="top">
                            <x-button variant="danger" size="md">반려</x-button>
                        </x-tooltip>
                        <x-tooltip text="다음 결재자에게 넘어갑니다" position="bottom">
                            <x-button variant="primary" size="md">승인</x-button>
                        </x-tooltip>
                        <x-tooltip text="오른쪽에 뜨는 툴팁" position="right">
                            <x-button variant="secondary" size="md">오른쪽</x-button>
                        </x-tooltip>
                    </div>
                    <p class="text-label-1 text-label-alternative">호버·포커스에 반응한다. 반경 6px · 11px 텍스트.</p>
                </x-card>

                <x-card>
                    <h3 class="mb-3 text-headline-2 font-semibold text-label-normal">Thumbnail · Profile</h3>
                    <div class="flex flex-wrap items-end gap-4">
                        <x-thumbnail name="김기안" size="xs" />
                        <x-thumbnail name="이대리" size="sm" />
                        <x-thumbnail name="박과장" size="md" />
                        <x-thumbnail name="최부장" size="lg" />
                        <x-thumbnail name="정이사" size="xl" />
                        <x-thumbnail name="사각" size="lg" shape="square" />
                        <x-thumbnail size="lg" />
                    </div>
                    <p class="mt-3 text-label-1 text-label-alternative">
                        Profile 은 원형, Thumbnail 은 4px 사각. 이미지가 없으면 이름 첫 글자, 이름도 없으면 아이콘.
                    </p>
                </x-card>
            </section>

            {{-- ═══ 아이콘 ═══ --}}
            <section id="icons" class="flex flex-col gap-4">
                <div>
                    <h2 class="text-title-3 font-bold text-label-normal">아이콘</h2>
                    <p class="mt-1 text-body-2 text-label-alternative">
                        아이콘 {{ $iconNames->count() }}종.
                        <code class="font-mono text-label-2">&lt;x-icon-{이름} class="h-5 w-5" /&gt;</code>
                        — 색·타이포는 원본 로 옮겼지만 아이콘 세트는 아직 청담원 DS 출처다.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-2 sm:grid-cols-5 lg:grid-cols-8">
                    @foreach ($iconNames as $name)
                        <div class="flex flex-col items-center gap-1.5 rounded-md border border-line-solid-alternative bg-background-normal px-2 py-3 text-label-neutral transition-colors hover:border-primary hover:text-primary">
                            <x-dynamic-component :component="'icon-' . $name" class="h-6 w-6" />
                            <code class="w-full truncate text-center font-mono text-caption-2 text-label-assistive" title="{{ $name }}">{{ $name }}</code>
                        </div>
                    @endforeach
                </div>
            </section>

            <footer class="border-t border-line-solid-normal pb-10 pt-6 text-label-1 text-label-assistive">
                이 페이지는 <code class="font-mono text-label-2">resources/views/styleguide.blade.php</code>.
                새 컴포넌트를 만들면 여기에도 추가한다 — 그래야 깨진 걸 바로 본다.
            </footer>
        </main>
    </div>
</x-layout>
