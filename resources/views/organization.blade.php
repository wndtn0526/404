{{-- 화상조직도 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-279525 "page")
     회사 아래로 조직을 세로로 잇고, 각 조직 카드에 조직장과 멤버를 담는다.
     우측 384 패널은 멤버 목록(검색 + 목록 + 멤버 추가).

     원본 실측(1920) — 캔버스 배경 Mono/Global BG · 카드 280 · 반경 6
       헤더 카드 82 · 본문 카드(조직장/멤버) · 접기 원 30 (카드 아래 테두리에 걸침)
       점선 세로선 · 조직 추가 버튼 24 · 반경 6 · 면 Mono/800
       우측 패널 384 (좌우 패딩 30) · 제목 20 Bold lh30 (DS heading-2)
       검색 인풋 324x40 · 목록 행 62 (안쪽 좌우 30) · 아바타 40
       좌하단 캔버스 이동 버튼 28 두 개

     회사 심볼은 LNB 레일과 같은 브랜드 마크(brand-cdw-mark)를 쓴다. 회사명은 청담원이다.

     ⚠️ 원본 자식 카드에는 이름 헤더가 없다. 조직이 둘 이상이면 구분이 안 돼 헤더를 붙였다
        (partials/org-node 주석 참고).
     ⚠️ 원본은 조직이 하나뿐인 세로 한 줄이다. 형제 조직이 여러 개가 되면 가로로 뻗는 트리
        레이아웃이 필요하다 — 그 디자인은 원본에 없어서 지금은 세로로만 잇는다.
     ⚠️ 좌하단 28px 버튼 두 개는 원본에 좌·우 화살표만 있고 무슨 동작인지 적혀 있지 않다.
        캔버스가 좁을 때 가로로 넘치므로 캔버스를 좌우로 밀는 버튼으로 뒀다.
     ⚠️ 조직 추가 · 멤버 추가 · 조직장 추가 · 더보기는 아직 동작하지 않는다.
        상태를 바꾸는 요청은 POST + CSRF 로 붙인다.
     ⚠️ 사람·조직 데이터는 뷰에 박아둔 예시다. DB 에서 오지 않는다. 인사 정보를 실제로 붙일
        때는 개인정보(주민번호 등)를 이 화면에 두지 않는다. --}}
@php
    // 회사(루트) — 심볼은 LNB 레일의 브랜드 마크를 그대로 쓴다.
    $company = ['name' => '청담원', 'mark' => 'cdw-mark', 'total' => 6];

    // 하위 조직. 원본이 세로 한 줄이라 순서대로 잇는다.
    $orgs = [
        [
            'name' => '개발팀', 'initial' => '개', 'tone' => 'bg-deep-blue-800', 'total' => 3,
            'leader' => ['name' => '김기안', 'role' => '개발팀장'],
            'members' => [
                ['name' => '이대리', 'role' => '프론트엔드 개발'],
                ['name' => '박사원', 'role' => '백엔드 개발'],
            ],
        ],
        [
            'name' => '운영팀', 'initial' => '운', 'tone' => 'bg-warm-gray-800', 'total' => 3,
            'leader' => null,
            'members' => [
                ['name' => '최주임', 'role' => '수강 운영'],
                ['name' => '정과장', 'role' => '정산 · 리포트'],
            ],
        ],
    ];

    // 우측 멤버 목록 — 회사 전체. 조직장과 멤버를 합친 수가 회사 '전체 N명' 과 같아야 한다.
    $members = [
        ['name' => '김기안', 'role' => '개발팀장'],
        ['name' => '이대리', 'role' => '프론트엔드 개발'],
        ['name' => '박사원', 'role' => '백엔드 개발'],
        ['name' => '최주임', 'role' => '수강 운영'],
        ['name' => '정과장', 'role' => '정산 · 리포트'],
        ['name' => '신고수', 'role' => '프로덕트 디자이너'],
    ];

    // 점선 연결선 — 세로 점선. 원본은 SVG 점선이라 border-dashed 로 대신했다.
    $connector = 'mx-auto h-[38px] w-0 border-l border-dashed border-warm-gray-300';
@endphp

<x-layout title="화상조직도">
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
            <x-breadcrumb :items="[['label' => '홈', 'href' => url('/workspace')], ['label' => '화상조직도']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">화상조직도</h1>
        </x-slot:title>

        <div class="flex min-w-0 flex-col gap-6 xl:flex-row xl:items-start">

            {{-- ═══ 캔버스 ═══ 조직 트리 --}}
            <div class="min-w-0 flex-1" x-data="{ scroll(px) { this.$refs.canvas.scrollBy({ left: px, behavior: 'smooth' }) } }">
                {{-- 캔버스에 배경을 깔지 않는다. 원본 캔버스가 곧 페이지 배경(Mono/Global BG)이고
                     셸의 페이지 배경이 이미 같은 색이다. 카드만 흰 면으로 떠 있다. --}}
                <div x-ref="canvas" class="min-w-0 overflow-x-auto py-10">
                    <div class="mx-auto flex w-[280px] flex-col items-center">

                        {{-- 회사 --}}
                        @include('partials.org-node', ['org' => $company, 'root' => true])

                        {{-- 하위 조직 — 접기 원 · 점선 · 조직 추가 버튼으로 잇는다 --}}
                        <div x-data="{ open: true }" class="flex w-full flex-col items-center">
                            {{-- 접기 원 30 — 원본은 헤더 카드 아래 테두리에 걸쳐 있다 --}}
                            <button type="button" @click="open = ! open"
                                    class="-mt-[15px] flex size-[30px] items-center justify-center rounded-full border border-line-solid-normal bg-background-normal text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                    x-bind:aria-expanded="open"
                                    aria-label="하위 조직 접기/펴기">
                                {{-- 접히면 화살표가 아래로 돈다. Alpine 이 안 붙어도 펴진 모양으로 남는다. --}}
                                <x-icon-chevron-up class="size-3.5 transition-transform" x-bind:class="open || 'rotate-180'" />
                            </button>

                            {{-- 접힌 상태에서도 조직 추가 자리는 남긴다(원본에 접힌 상태가 없어 이렇게 뒀다) --}}
                            <div class="flex w-full flex-col items-center" x-show="open" x-cloak>
                                @foreach ($orgs as $org)
                                    <div class="{{ $connector }}" aria-hidden="true"></div>
                                    <button type="button"
                                            class="flex size-6 items-center justify-center rounded-lg bg-mono-800 text-white transition-colors hover:bg-mono-black focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                            aria-label="조직 추가">
                                        <x-icon-plus class="size-3.5" />
                                    </button>
                                    <div class="{{ $connector }}" aria-hidden="true"></div>

                                    <div class="flex w-full flex-col items-center">
                                        @include('partials.org-node', ['org' => $org])
                                    </div>
                                @endforeach

                                {{-- 마지막 아래에도 조직을 더 붙일 수 있다 --}}
                                <div class="{{ $connector }}" aria-hidden="true"></div>
                                <button type="button"
                                        class="flex size-6 items-center justify-center rounded-lg bg-mono-800 text-white transition-colors hover:bg-mono-black focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                        aria-label="조직 추가">
                                    <x-icon-plus class="size-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 캔버스 좌우 이동 — 원본 좌하단 28px 버튼 두 개 --}}
                <div class="mt-3 flex items-center gap-2">
                    <button type="button" @click="scroll(-320)"
                            class="flex size-7 items-center justify-center rounded-md border border-line-solid-normal bg-background-normal text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                            aria-label="캔버스를 왼쪽으로 이동">
                        <x-icon-chevron-left class="size-3.5" />
                    </button>
                    <button type="button" @click="scroll(320)"
                            class="flex size-7 items-center justify-center rounded-md border border-line-solid-normal bg-background-normal text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                            aria-label="캔버스를 오른쪽으로 이동">
                        <x-icon-chevron-right class="size-3.5" />
                    </button>
                </div>
            </div>

            {{-- ═══ 멤버 목록 384 ═══ --}}
            <aside class="w-full min-w-0 shrink-0 rounded-lg bg-background-normal pb-5 xl:w-96">
                <div class="flex min-w-0 items-center gap-3 px-[30px] pt-[30px]">
                    <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">멤버 목록</h2>
                    <p class="text-label-1 leading-5 text-warm-gray-600">
                        총 <span class="tabular-nums">{{ count($members) }}</span>명
                    </p>
                    <button type="button"
                            class="ml-auto inline-flex size-6 shrink-0 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                            aria-label="멤버 목록 더보기">
                        <x-icon-more-horizontal class="size-6" />
                    </button>
                </div>

                {{-- 검색 — 원본 324x40. 정적 화면이라 실제 조회는 없다. --}}
                <div class="px-[30px] pt-5">
                    <x-input name="member_search" size="md" icon="search" placeholder="멤버 이름이나 조직 검색" />
                </div>

                <div class="pt-2.5">
                    @foreach ($members as $member)
                        <div class="flex h-[62px] min-w-0 items-center gap-3 px-[30px]">
                            <x-thumbnail :name="$member['name']" size="md" shape="circle" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $member['name'] }}</p>
                                <p class="truncate text-caption-1 leading-[18px] text-warm-gray-600">{{ $member['role'] }}</p>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" class="group flex h-[62px] w-full min-w-0 items-center gap-3 px-[30px] text-left focus:outline-none focus-visible:bg-fill-alternative">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-warm-gray-100 text-label-normal transition-colors group-hover:bg-warm-gray-200">
                            <x-icon-plus class="size-6" />
                        </span>
                        <span class="truncate pl-0.5 text-label-1 leading-5 text-warm-gray-500 transition-colors group-hover:text-label-normal">
                            멤버 추가
                        </span>
                    </button>
                </div>
            </aside>
        </div>
    </x-workspace-shell>
</x-layout>
