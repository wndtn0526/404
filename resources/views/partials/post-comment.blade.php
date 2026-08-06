{{-- 댓글 한 줄 — 게시글 상세(post.blade.php) 전용. 대댓글까지 자기 자신을 다시 부른다.
     세로선과 '대댓글 N개' 가 부모 댓글의 본문 열 왼쪽 끝에 서야 해서(원본 x68)
     대댓글 목록을 본문 열 안에 넣었다. 밖으로 빼면 들여쓰기를 손으로 맞춰야 한다.

     원본 실측 (Figma GPRO_PORTFOLIO node 1104-59293 · 375 기준)
       아바타 38(대댓글 32) · 아바타↔본문 10 · 이름↔역할 4 · 이름↔본문 5 · 본문↔메타 5
       메타 항목 사이 10 · 대댓글 세로선 1px Warm gray/200 · 세로선↔아바타 10
       이름 13 Bold lh20 · 역할·메타 11 lh17 Warm gray/600 · 본문 13 lh20 -0.2
     PC(lg) 는 피드 카드(node 1104-55195) 실측 — 아바타 48 · 이름 15 · 본문 15 · 메타 13.

     ⚠️ 아바타는 DS 썸네일 단계가 32/40/48 이라 원본 38 을 40 으로 올렸다(2px).
        브레이크포인트마다 단계가 바뀌므로 두 번 렌더하고 변형 클래스로 하나만 보인다.
        변형(lg: · max-lg:)은 컴포넌트가 내보내는 inline-flex 를 항상 이긴다.

     변수:
       comment : ['author','role','when','text', 'replies' => [...], 'more' => '3']
       nested  : 대댓글이면 true — 아바타가 한 단계 작아진다 --}}
@php
    $nested = $nested ?? false;
    $metaText = 'text-caption-2 leading-[17px] text-warm-gray-600 lg:text-label-2 lg:leading-5';
@endphp

<div class="flex min-w-0 items-start gap-2.5">
    <x-thumbnail :name="$comment['author']" :size="$nested ? 'sm' : 'md'" shape="circle" class="lg:hidden" />
    <x-thumbnail :name="$comment['author']" :size="$nested ? 'md' : 'lg'" shape="circle" class="max-lg:hidden" />

    <div class="min-w-0 flex-1">
        {{-- 이름 · 역할 — 원본 간격 4.
             ⚠️ 원본의 세 번째 댓글만 이 간격이 37 이다. 긴 이름을 지운 흔적으로 보고 4 로 맞췄다. --}}
        <div class="flex min-w-0 flex-wrap items-baseline gap-1 lg:gap-2">
            <p class="text-label-2 font-bold leading-5 text-mono-black lg:text-body-2 lg:leading-[21px]">
                {{ $comment['author'] }}
            </p>
            <p class="{{ $metaText }}">{{ $comment['role'] }}</p>
        </div>

        <p class="pt-[5px] text-label-2 leading-5 text-mono-black lg:pt-2 lg:text-body-2 lg:leading-[23px]">
            {{ $comment['text'] }}
        </p>

        <div class="flex flex-wrap items-baseline gap-2.5 pt-[5px] lg:pt-[9px]">
            <span class="{{ $metaText }}">{{ $comment['when'] }}</span>
            <button type="button" class="{{ $metaText }} font-bold transition-colors hover:text-label-normal">
                답글 달기
            </button>
        </div>

        {{-- 대댓글 — 세로선이 이 열의 왼쪽 끝에 선다. 원본은 선 높이가 목록과 정확히 같으므로
             위쪽 간격 20 은 테두리 밖(감싼 div)에 둔다. 안에 두면 선이 20 만큼 위로 삐져나온다. --}}
        @if (! empty($comment['replies']))
            <div class="pt-5">
                <ul class="flex flex-col gap-5 border-l border-warm-gray-200 pl-2.5">
                    @foreach ($comment['replies'] as $reply)
                        <li class="min-w-0">
                            @include('partials.post-comment', ['comment' => $reply, 'nested' => true])
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 대댓글 더보기 — 원본 아이콘 18 · 아이콘↔글자 5 --}}
        @if (! empty($comment['more']))
            <button type="button"
                    class="flex items-center gap-[5px] pt-3 text-label-2 leading-5 text-warm-gray-600 transition-colors hover:text-label-normal lg:pt-4 lg:text-body-2 lg:leading-[23px]">
                <x-icon-arrow-turn-down-right class="size-[18px] shrink-0" />
                <span>대댓글 {{ $comment['more'] }}개</span>
            </button>
        @endif
    </div>
</div>
