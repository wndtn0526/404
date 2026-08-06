{{-- 화상조직도 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1002-279525 "page")
     처음 들어갔을 때, 멤버가 본인 한 명뿐인 상태다. 원본 화면이 그 상태를 그린 것이다.

     원본 실측(1920) — 캔버스 배경 Mono/Global BG · 카드 280 · 반경 6
       회사 헤더 카드 82 (패딩 20 · 심볼 42 · 이름 15 Bold · 전체 N명 12 Warm gray/600)
       접기 원 30 (헤더 카드 아래 테두리에 걸침) · 점선 세로선
       조직 추가 버튼 24 · 반경 6 · 면 Mono/800
       조직 카드 277 (조직장 / 구분선 240 / 멤버 (N) / 멤버 추가) · 행 62 · 안쪽 좌우 20
         추가 버튼 40 원형 면 Warm gray/100 · 글자 14 Warm gray/500
       우측 패널 384 (좌우 패딩 30) · 제목 20 Bold lh30 (DS heading-2)
         검색 324x40 · 목록 행 62 (안쪽 좌우 30) · 아바타 40
       좌하단 캔버스 이동 버튼 28 두 개

     타이포는 원본값이 DS 토큰과 그대로 맞는다 —
       이름 15 Bold lh23 -0.6 = body-2 · 역할·전체 N명 12 lh18 -0.2 = caption-1
       추가 글자 14 lh20 -0.2 = label-1 · 패널 제목 20 Bold lh30 -1 = heading-2

     회사 심볼은 LNB 레일과 같은 브랜드 마크(brand-cdw-mark)를 쓴다. 회사명은 청담원이다.

     ⚠️ 원본에는 브레드크럼·페이지 제목이 없다(GNB 아래 바로 캔버스다). 다른 워크스페이스
        화면과 위치를 맞추려고 셸의 브레드크럼·제목 슬롯을 그대로 썼다.
     ⚠️ 원본 조직 카드에는 이름 헤더가 없다. 조직이 하나뿐이라 구분할 필요가 없어서다.
        조직이 둘 이상이 되면 partials/org-node 의 header 를 함께 켜야 한다.
     ⚠️ 형제 조직이 여러 개가 되면 가로로 뻗는 트리 레이아웃이 필요하다 — 그 디자인은
        원본에 없다. 지금은 원본대로 세로 한 줄이다.
     ⚠️ 좌하단 28px 버튼 두 개는 원본에 좌·우 화살표만 있고 동작이 적혀 있지 않다. 캔버스가
        넘칠 때 좌우로 밀는 버튼으로 뒀다. 세로 한 줄인 지금은 넘치지 않아 움직이지 않는다.
     ⚠️ 조직 추가 · 조직장 추가 · 멤버 추가 · 더보기는 아직 동작하지 않는다.
        상태를 바꾸는 요청은 POST + CSRF 로 붙인다.
     ⚠️ 사람·조직 데이터는 뷰에 박아둔 예시다. 인사 정보를 실제로 붙일 때는
        개인정보(주민번호 등)를 이 화면에 두지 않는다. --}}
@php
    /*
     * 본인 한 명 — 워크스페이스를 만든 사람이다. 다른 화면들이 셸에 넘기는 사용자와 같다.
     * 조직이 아직 없으니 직무가 아니라 워크스페이스에서의 역할을 적었다.
     */
    $me = ['name' => '김기안', 'role' => '관리자'];

    // 회사(루트) — 심볼은 LNB 레일의 브랜드 마크를 그대로 쓴다.
    $company = ['name' => '청담원', 'mark' => 'cdw-mark', 'total' => 1];

    // 조직 하나 — 조직장은 아직 없고 멤버는 본인뿐이다(원본 그대로).
    $org = ['name' => '청담원', 'leader' => null, 'members' => [$me]];

    // 우측 멤버 목록 — 회사 전체. 회사 카드의 '전체 N명' 과 수가 같아야 한다.
    $members = [$me];

    // 점선 연결선 — 원본은 SVG 점선이라 border-dashed 로 대신했다.
    $connector = 'mx-auto h-[38px] w-0 border-l border-dashed border-warm-gray-300';
    $plusBtn = 'flex size-6 items-center justify-center rounded-lg bg-mono-800 text-white transition-colors hover:bg-mono-black focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
    $navBtn = 'flex size-7 items-center justify-center rounded-md border border-line-solid-normal bg-background-normal text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<x-layout title="화상조직도">
    <x-workspace-shell
        workspace="청담원"
        domain="cdw.workspace.io"
        :user="$me['name']"
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

        {{-- items-stretch: 원본 우측 패널은 높이 976 로 화면을 거의 다 채운다. 내용만큼만
             높으면 캔버스 옆에서 짧아 보인다. 두 열을 같은 높이로 늘려서 맞춘다. --}}
        <div class="flex min-w-0 flex-col gap-6 xl:flex-row xl:items-stretch">

            {{-- ═══ 캔버스 ═══ 회사 → 조직 --}}
            <div class="min-w-0 flex-1" x-data="{ scroll(px) { this.$refs.canvas.scrollBy({ left: px, behavior: 'smooth' }) } }">
                {{-- 캔버스에 배경을 깔지 않는다. 원본 캔버스가 곧 페이지 배경(Mono/Global BG)이고
                     셸의 페이지 배경이 이미 같은 색이다. 카드만 흰 면으로 떠 있다. --}}
                <div x-ref="canvas" class="min-w-0 overflow-x-auto py-10">
                    <div class="mx-auto flex w-[280px] flex-col items-center">

                        {{-- 회사 — 이름 카드만 --}}
                        @include('partials.org-node', ['org' => $company, 'body' => false])

                        <div x-data="{ open: true }" class="flex w-full flex-col items-center">
                            {{-- 접기 원 30 — 원본은 헤더 카드 아래 테두리에 걸쳐 있다 --}}
                            <button type="button" @click="open = ! open"
                                    class="-mt-[15px] flex size-[30px] items-center justify-center rounded-full border border-line-solid-normal bg-background-normal text-label-normal transition-colors hover:bg-fill-alternative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                    x-bind:aria-expanded="open"
                                    aria-label="하위 조직 접기/펴기">
                                {{-- 접히면 화살표가 아래로 돈다. Alpine 이 안 붙어도 펴진 모양으로 남는다. --}}
                                <x-icon-chevron-up class="size-3.5 transition-transform" x-bind:class="open || 'rotate-180'" />
                            </button>

                            <div class="flex w-full flex-col items-center" x-show="open" x-cloak>
                                <div class="{{ $connector }}" aria-hidden="true"></div>
                                <button type="button" class="{{ $plusBtn }}" aria-label="조직 추가">
                                    <x-icon-plus class="size-3.5" />
                                </button>
                                <div class="{{ $connector }}" aria-hidden="true"></div>

                                {{-- 조직 — 조직장·멤버 카드만(원본에 이름 헤더가 없다) --}}
                                @include('partials.org-node', ['org' => $org, 'header' => false])

                                {{-- 아래로 조직을 더 붙일 수 있다 --}}
                                <div class="{{ $connector }}" aria-hidden="true"></div>
                                <button type="button" class="{{ $plusBtn }}" aria-label="조직 추가">
                                    <x-icon-plus class="size-3.5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 캔버스 좌우 이동 — 원본 좌하단 28px 버튼 두 개 --}}
                <div class="mt-3 flex items-center gap-2">
                    <button type="button" @click="scroll(-320)" class="{{ $navBtn }}" aria-label="캔버스를 왼쪽으로 이동">
                        <x-icon-chevron-left class="size-3.5" />
                    </button>
                    <button type="button" @click="scroll(320)" class="{{ $navBtn }}" aria-label="캔버스를 오른쪽으로 이동">
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
