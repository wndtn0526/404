{{-- 커리어 패널 섹션 제목 — 네 섹션이 같은 모양이라 하나로 뺐다.
     원본 모바일(node 1104-59365)의 List Default 컴포넌트다.

     PC  실측 (node 1104-58476) — 20 Bold lh30 · pt17 pb8 · 좌우 30 · 더보기 검정
     모바일 실측 (node 1104-59365) — 15 Bold lh23 · 행 높이 30 · 좌우 20
                                    더보기 Warm gray/400 (PC 와 색이 다르다)

     위쪽 여백 30 은 감싼 <section> 의 pt 가 낸다 — 여기서 margin 으로 주면
     패딩 없는 부모를 통과해 섹션 자체가 밀린다(마진 상쇄).

     $heading = 섹션 제목 --}}
<div class="flex h-[30px] items-center justify-between px-5 lg:h-auto lg:px-[30px] lg:pb-2 lg:pt-[17px]">
    <h2 class="truncate text-body-2 font-bold leading-[23px] text-mono-black lg:text-heading-2 lg:leading-[30px]">
        {{ $heading }}
    </h2>
    <button type="button"
            class="inline-flex size-6 shrink-0 items-center justify-center text-warm-gray-400 transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 lg:text-label-normal"
            aria-label="{{ $heading }} 더보기">
        <x-icon-more-horizontal class="size-6" />
    </button>
</div>
