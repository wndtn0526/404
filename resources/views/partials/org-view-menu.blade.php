{{-- 멤버 목록 패널의 '···' 를 누르면 열리는 보기 설정 메뉴 — Figma node 1002-279153
     (안쪽 contents 프레임 1002-279218).

     원본 실측 — 패널 384x371 · 반경 6 · 패딩 30 · 버튼 오른쪽 아래(우측 정렬)에서 열린다
       소제목 12 Bold lh18 -0.2 Warm gray/500  → DS caption-1 과 정확히 일치
       항목 15 Bold lh23 -0.6                  → DS body-2 와 정확히 일치
       소제목 → 첫 항목 18 · 항목 사이 24 · 구분선 324 · 구분선 위 19 아래 18
       단축키 배지 24x24 (⇧ 붙은 것은 39x24) · 반경 4
         아래 1px 만 Warm gray/300, 면은 Warm gray/100, 글자 14 Medium Warm gray/400
       토글 45x27

     항목:
       보기 설정 — 조직도 모두 펼치기 [F] · 조직도 모두 접기 [⇧F] · 멤버 목록 [토글]
       기타 설정 — 화상 조직도 튜토리얼 · 단축키 보기

     ⚠️ 토글은 DS x-switch sm(40x24)이다. 원본 45x27 과 한 단계 차이다.
     ⚠️ 배지의 F · ⇧F 는 실제로 동작한다(부모 x-data 에 keydown 핸들러가 있다).
        입력 칸에 포커스가 있을 때는 먹지 않는다.
     ⚠️ '화상 조직도 튜토리얼' · '단축키 보기' 는 아직 갈 곳이 없다.

     부모 x-data 에서 쓰는 상태:
       expanded    조직도 펼침 여부
       membersOpen 멤버 목록 펼침 여부 --}}
@php
    $rowBase = 'flex w-full min-w-0 items-center justify-between gap-4 text-left focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4';
    $rowText = 'truncate text-body-2 font-bold leading-[23px] text-mono-black';
    $subhead = 'text-caption-1 font-bold leading-[18px] text-warm-gray-500';
    // 키캡 — 원본은 두 겹(아래 1px 만 진한 색)이다. border-b 로 같은 모양을 낸다.
    $cap = 'flex h-6 items-center justify-center gap-0.5 rounded-md border-b-[1.5px] border-warm-gray-300 bg-warm-gray-100 px-1.5 text-label-1 font-medium leading-5 text-warm-gray-400';
@endphp

<div class="relative" x-data="{ open: false }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    <button type="button" @click="open = ! open"
            class="inline-flex size-6 shrink-0 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
            x-bind:aria-expanded="open"
            aria-haspopup="true"
            aria-label="보기 설정">
        <x-icon-more-horizontal class="size-6" />
    </button>

    {{-- 버튼 오른쪽 끝에 맞춰 아래로 열린다(원본 그대로) --}}
    <div x-show="open" x-cloak role="menu"
         class="absolute right-0 top-full z-30 w-96 rounded-lg bg-background-normal p-[30px] shadow-elevation-lg">

        <p class="{{ $subhead }}">보기 설정</p>

        <div class="mt-[18px] flex flex-col gap-6">
            <button type="button" role="menuitem" class="{{ $rowBase }}"
                    @click="expanded = true; open = false">
                <span class="{{ $rowText }}">조직도 모두 펼치기</span>
                <span class="{{ $cap }}" aria-hidden="true">F</span>
            </button>

            <button type="button" role="menuitem" class="{{ $rowBase }}"
                    @click="expanded = false; open = false">
                <span class="{{ $rowText }}">조직도 모두 접기</span>
                <span class="{{ $cap }}" aria-hidden="true">
                    <x-icon-arrow-up class="size-3" />F
                </span>
            </button>

            {{-- 멤버 목록 — 켜고 끄는 것이 이 패널 본문이다. 패널 헤더와 이 메뉴는 남으므로
                 끈 뒤에도 다시 켤 수 있다.
                 ⚠️ 원본은 패널 전체를 감추는 것으로 보이는데, 그러면 이 메뉴까지 사라져
                    되돌릴 길이 없다. 본문만 접는 쪽으로 뒀다. --}}
            <div class="flex w-full min-w-0 items-center justify-between gap-4">
                <span class="{{ $rowText }}">멤버 목록</span>
                <x-switch size="sm" x-model="membersOpen" class="shrink-0" />
            </div>
        </div>

        <div class="mt-5 h-px bg-warm-gray-100" aria-hidden="true"></div>

        <p class="{{ $subhead }} mt-[18px]">기타 설정</p>

        <div class="mt-[18px] flex flex-col gap-6">
            <button type="button" role="menuitem" class="{{ $rowBase }}">
                <span class="{{ $rowText }}">화상 조직도 튜토리얼</span>
            </button>
            <button type="button" role="menuitem" class="{{ $rowBase }}">
                <span class="{{ $rowText }}">단축키 보기</span>
            </button>
        </div>
    </div>
</div>
