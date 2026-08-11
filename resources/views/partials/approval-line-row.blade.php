{{-- 결재선 한 줄 — Figma GPRO_PORTFOLIO 'List Default' (node 1002:115053~115056)
     진행 묶음과 열람·참조 묶음이 같은 모양이라 한 벌만 두고 양쪽에서 부른다.
     Alpine x-for 안에서 부르므로 `m` 이 있어야 한다.

     원본 실측 — 줄 324x56 · 아바타 42 · 이름 15 Bold lh23 · 소속 11 lh17 Warm gray/500
                 역할 14 lh20 검정 우측

     ⚠️ 원본 아바타는 사진이다. 이 저장소는 다른 화면과 같이 x-thumbnail(머리글자)을 쓴다.
        md 가 40 이라 원본 42 보다 2 작다.
     ⚠️ 삭제(X)는 원본에 없다. 결재선 팝업을 다시 열지 않고도 뗄 수 있어야 해서
        줄에 마우스를 올릴 때만 역할 글자 자리에 겹쳐 나온다. 가만히 두면 원본 그대로다. --}}
<div class="group/line flex h-14 min-w-0 items-center gap-2">
    <x-thumbnail name-expr="m.name" size="md" shape="circle" class="shrink-0" />

    <div class="min-w-0 flex-1">
        <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black" x-text="m.name"></p>
        <p class="truncate text-caption-2 leading-[17px] text-label-alternative" x-text="m.dept"></p>
    </div>

    <span class="relative shrink-0">
        <span class="text-label-1 leading-5 text-mono-black transition-opacity group-hover/line:opacity-0 group-focus-within/line:opacity-0"
              x-text="({ progress: '승인', view: '열람', ref: '참조' })[m.role]"></span>

        <button type="button" @click="dropPick(m.name)"
                class="absolute inset-y-0 right-0 flex items-center text-label-alternative opacity-0 transition-opacity hover:text-label-normal focus:opacity-100 focus:outline-none group-hover/line:opacity-100"
                aria-label="결재자 떼기">
            <x-icon-close class="size-[18px]" />
        </button>
    </span>
</div>
