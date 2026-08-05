{{-- 컨텐츠 관리 — Figma 워크스페이스 화면 (Lnsej46BaxtyKq3rhssFH3 · node 1-299)
     원본은 본문이 비어 있는 화면 틀이다. 표·필터가 붙기 전까지 그대로 둔다.

     LNB·GNB·헤더 크롬은 <x-workspace-shell> 이 갖고 있다. 이 파일은 화면 고유의
     브레드크럼·타이틀·탭·기준일만 채운다. --}}
<x-layout title="컨텐츠 관리">
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
            <x-breadcrumb :items="[['label' => '홈', 'href' => '#'], ['label' => '컨텐츠 관리']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">컨텐츠 관리</h1>

            {{-- 워크스페이스 탭 — 원본 Box34. 활성은 검정 채움 + 닫기, 비활성은 Warm gray/200.
                 DS <x-tabs> 는 밑줄형이라 형태가 다르다. 원본이 알약형이어서 여기서 조립했다. --}}
            <div class="flex items-start gap-4">
                <span class="inline-flex items-center justify-center gap-1 rounded-lg bg-mono-black pb-[5px] pl-3 pr-2.5 pt-1.5">
                    <span class="text-body-2 font-bold leading-[23px] text-white">컨텐츠 관리</span>
                    <button type="button"
                            class="inline-flex shrink-0 items-center pb-px text-white transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                            aria-label="컨텐츠 관리 탭 닫기">
                        <x-icon-close class="size-[18px]" />
                    </button>
                </span>

                <a href="#"
                   class="inline-flex items-center justify-center rounded-lg bg-warm-gray-200 px-3 pb-[5px] pt-1.5 text-body-2 font-bold leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">
                    과정 관리
                </a>
            </div>
        </x-slot:title>

        <x-slot:actions>
            {{-- 기준일 — 원본 우측 정렬. 라벨 Bold · 날짜 Regular · 14px --}}
            <button type="button"
                    class="inline-flex items-center gap-2 text-label-1 text-mono-black transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                <span class="font-bold">기준일</span>
                <span>2021.08.01</span>
                <x-icon-caret-down class="size-3.5 shrink-0" />
            </button>
        </x-slot:actions>

        {{-- 원본의 본문은 비어 있다. --}}
    </x-workspace-shell>
</x-layout>
