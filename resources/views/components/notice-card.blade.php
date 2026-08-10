{{-- 알림 카드 — Figma GPRO_PORTFOLIO '홈카드' (node 1002-106150).
     "누가 무엇을 했다" 한 줄 + 상태·시각 + 오른쪽 액션. 확인할 문서 목록이 이걸로 쌓인다.
     원본 이름이 '홈카드' 인 걸 보면 워크스페이스 홈에서도 같은 카드를 쓴다.

     원본 실측 — 카드 1200x102 · 반경 6 · 패딩 30 · 칸 사이 20
       프로필 40
       윗줄 14 Bold lh20 -0.2 + 안 읽음 점 6 (Secondary/red 800) · 사이 2
       아랫줄 사이 10 — 상태 12 Medium 검정 · 시각 12 Regular Warm gray/500 · 윗줄에서 4
       액션 버튼 면 Warm gray/100 · 반경 4 · px 12 py 6 · 글자 12 Bold

     ⚠️ 액션은 DS x-button 을 쓴다. sm 이 40 이라 원본 30 보다 한 단계 크다 —
        이 저장소의 다른 화면과 같은 차이다.

     props
       name    : 아바타에 쓸 이름
       message : 윗줄 문장
       state   : 아랫줄 상태 (예: 결재 진행중)
       time    : 아랫줄 시각 (예: 1분 전)
       unread  : 안 읽음 점
       action  : 오른쪽 버튼 글자. 없으면 버튼을 안 그린다.
       href    : 주면 버튼이 링크가 된다 --}}
@props([
    'name' => null,
    'message' => null,
    'state' => null,
    'time' => null,
    'unread' => false,
    'action' => null,
    'href' => null,
])

<div {{ $attributes->class('flex min-w-0 items-center gap-5 rounded-lg bg-background-normal p-[30px]') }}>
    <div class="flex min-w-0 flex-1 items-start gap-5">
        <x-thumbnail :name="$name" size="md" shape="circle" class="shrink-0" />

        <div class="flex min-w-0 flex-col gap-1">
            <div class="flex min-w-0 items-start gap-0.5">
                <p class="min-w-0 text-label-1 font-bold leading-5 text-mono-black">{{ $message }}</p>
                @if ($unread)
                    {{-- 안 읽음 — 원본은 문장 끝에 붙는 6px 점이다 --}}
                    <span class="mt-0.5 size-1.5 shrink-0 rounded-full bg-red-800" aria-label="안 읽음"></span>
                @endif
            </div>

            <div class="flex min-w-0 flex-wrap items-start gap-2.5">
                @if ($state)
                    <span class="text-caption-1 font-medium leading-[18px] text-mono-black">{{ $state }}</span>
                @endif
                @if ($time)
                    <span class="text-caption-1 leading-[18px] text-label-alternative">{{ $time }}</span>
                @endif
            </div>
        </div>
    </div>

    @if ($action)
        <x-button variant="secondary" size="sm" :href="$href" class="shrink-0">{{ $action }}</x-button>
    @endif
</div>
