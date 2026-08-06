{{-- 게시글 상세 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1104-59293 "Mobile iOS")
     퍼블릭 스페이스 피드의 첫 카드를 펼친 화면이다. 제목·본문·좋아요 15·댓글 7 이
     public-space 의 $feed[0] 과 같은 글이라 같은 값을 쓴다.

     원본은 모바일 한 장(375)뿐이다. 375 실측을 그대로 옮기고, PC(lg 이상)는
     피드 카드(node 1104-55195) 실측을 따라 690 카드 안에서 한 단계 키웠다.
     ⚠️ PC 레이아웃은 원본에 없다. Figma 에 PC 노드가 생기면 그 값으로 교체한다.

     원본 실측(375) — 좌우 여백 20 · 프로필 38 · 아바타↔본문 10(글쓴이 줄만 8)
        프로필 top 90 · 이름 13 Bold lh20 · 역할·시간 11 lh17 Warm gray/600 (이름 아래 1)
        타이틀 top 148 (15 Bold lh23 -0.6) · 본문 top 181 (13 lh20 -0.2, 4줄)
        반응 top 291 (아이콘 24 · 숫자 13 Bold · 간격 8/16/8, 공유는 우측 끝)
        구분선 top 330 (1px Warm gray/100) · 첫 댓글 top 351 · 댓글 사이 20
        대댓글 블록 left 68 · 세로선 1px Warm gray/200 h155 · 대댓글 아바타 32
        대댓글 더보기 top 819 (아이콘 18) · 입력바 top 859 h38 반경 full BG Warm gray/050
        입력바는 아바타가 알약 왼쪽 끝에 겹친다(글자 left 68 = 알약 좌측 + 48)

     ⚠️ 원본의 iOS 상태바·홈 인디케이터는 네이티브 크롬이라 옮기지 않았다. 상단 뒤로
        화살표만 살려 워크스페이스 셸 안에 넣었다. 셸 GNB 의 왼쪽 버튼은 LNB 접기다.
     ⚠️ 원본 모바일은 화면 전체가 흰색이다. 셸의 페이지 배경(Mono/Global BG)은 그대로 두고
        본문만 흰 면으로 깔았다. 모바일에서는 좌우로 흘려서 원본과 같은 여백 20 이 된다.
     ⚠️ 원본 작성자는 'Gpro' 다. 퍼블릭 스페이스와 같은 인물이라 신고수로 맞췄다.
     ⚠️ 원본은 같은 버튼을 최상위 댓글에서 '답글 달기', 대댓글·마지막 댓글에서 '댓글 달기' 로
        갈라 쓴다. 한 노드 안에서 갈린 것이라 실수로 보고 '답글 달기' 로 통일했다.
     ⚠️ 프로필은 원본이 스톡 일러스트다. 저장소가 public 이라 DS 썸네일(이니셜)로 뒀다.
     ⚠️ 원본 좋아요(like_24)는 꽉 찬 빨간 하트다. DS 아이콘 219종에 채워진 하트가 없어
        외곽선 하트에 색만 입혔다. 피드 카드도 같은 처리다. DS 에 채워진 하트가 들어오면 교체한다.
     ⚠️ 본문은 예시다. DB 에서 오지 않는다. --}}
@php
    $post = [
        'author' => '신고수',
        'role' => '프로덕트 디자이너',
        'when' => '하루 전',
        'title' => '스냅챗이 플랫폼으로 변화한다.',
        'body' => [
            '앱 안에서 간단하게 사용할 수 있는 스냅 미니를 탑재했다.',
            '스냅챗은 ’10초 내 사라지는 메시지’로 알려진 메신저이며 북미에서 MAU 기준 와츠앱을 제치고 2위의 위치를 점유하고 있다. 사용자 수는 페이스북 메신저보다 적지만 Gen Z의 사용 비율이 높다.',
        ],
        'likes' => '15',
        'comments' => '7',
    ];

    $comments = [
        ['author' => 'Bluecookie', 'role' => 'iOS 개발자', 'when' => '1분 전',
            'text' => '넵! 좋을 거 같네요:)'],
        ['author' => 'Hi.jang', 'role' => '마케터', 'when' => '1분 전',
            'text' => '여러 툴을 써봤지만 프레이머 쓸 거 아니면 피그마가 가장 합리적인 선택이라고 생각한다. 적응해보고 자세한 후기를 남겨보겠다.',
            'replies' => [
                ['author' => 'Anothertime', 'role' => '기획자', 'when' => '1분 전',
                    'text' => '넵! 좋을 거 같네요:)'],
                ['author' => 'Hi.jang', 'role' => '마케터', 'when' => '1분 전',
                    'text' => '적응해보고 자세한 후기를 남겨보겠다.'],
            ]],
        ['author' => '신고수', 'role' => '프로덕트 디자이너', 'when' => '1분 전',
            'text' => '앱 안에서 간단하게 사용할 수 있는 스냅 미니를 탑재했다.',
            'more' => '3'],
    ];

    $cardIcon = 'inline-flex size-6 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<x-layout :title="$post['title']">
    <x-workspace-shell
        workspace="Public Space"
        domain="dinopublic.io"
        user="김기안"
        has-alarm
        :rail="config('workspace.rail')"
        :items="config('workspace.public_items')"
        :footer-items="config('workspace.footer_items')"
        :scale="config('workspace.lnb_scale')"
    >
        <div class="mx-auto w-full min-w-0 max-w-[690px]">

            {{-- 뒤로 — 원본은 iOS 내비바 좌측의 8x15 셰브론이다.
                 -ml-2 는 36px 버튼 안의 20px 아이콘을 본문 왼쪽 끝에 맞추는 값이다. --}}
            <a href="{{ url('/public-space') }}"
               class="-ml-2 inline-flex size-9 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
                <x-icon-chevron-left class="size-5" />
                <span class="sr-only">퍼블릭 스페이스로 돌아가기</span>
            </a>

            {{-- -mx-5 는 셸 본문의 좌우 여백(20)을 되돌린다. 모바일에서 흰 면이 화면 끝까지
                 가고 내부 패딩 20 이 원본의 x20 이 된다. lg 부터는 690 카드로 돌아온다. --}}
            <article class="-mx-5 mt-2 min-w-0 bg-background-normal p-5 lg:mx-0 lg:rounded-lg lg:p-[30px]">

                {{-- ── 글쓴이 ── --}}
                <div class="flex min-w-0 items-start gap-2.5">
                    <x-thumbnail :name="$post['author']" size="md" shape="circle" class="lg:hidden" />
                    <x-thumbnail :name="$post['author']" size="lg" shape="circle" class="max-lg:hidden" />

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-label-2 font-bold leading-5 text-mono-black lg:text-body-2 lg:leading-[21px]">
                            {{ $post['author'] }}
                        </p>
                        <p class="truncate pt-px text-caption-2 leading-[17px] text-warm-gray-600 lg:pt-[5px] lg:text-label-2 lg:leading-5">
                            {{ $post['role'] }} · {{ $post['when'] }}
                        </p>
                    </div>

                    <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="더보기">
                        <x-icon-more-horizontal class="size-6" />
                    </button>
                </div>

                {{-- ── 제목 · 본문 ── --}}
                <h1 class="pt-5 text-body-2 font-bold leading-[23px] text-mono-black lg:text-headline-2 lg:leading-[27px]">
                    {{ $post['title'] }}
                </h1>

                {{-- 원본은 두 줄이 한 텍스트 블록이라 단락 사이 간격이 없다 --}}
                <div class="flex flex-col pt-2.5 lg:pt-3">
                    @foreach ($post['body'] as $para)
                        <p class="text-label-2 leading-5 text-mono-black lg:text-body-2 lg:leading-[23px]">{{ $para }}</p>
                    @endforeach
                </div>

                {{-- ── 반응 — 좋아요·댓글 좌측, 공유 우측 ── --}}
                <div class="flex items-center gap-2 pt-[30px] lg:pt-9">
                    <button type="button" class="{{ $cardIcon }}" aria-label="좋아요">
                        <x-icon-heart class="size-6 text-status-negative" />
                    </button>
                    <span class="text-label-2 font-bold leading-5 text-mono-black tabular-nums lg:text-body-2 lg:leading-[21px]">
                        {{ $post['likes'] }}
                    </span>

                    <button type="button" class="{{ $cardIcon }} ml-2" aria-label="댓글">
                        <x-icon-bubble class="size-6" />
                    </button>
                    <span class="text-label-2 font-bold leading-5 text-mono-black tabular-nums lg:text-body-2 lg:leading-[21px]">
                        {{ $post['comments'] }}
                    </span>

                    <button type="button" class="{{ $cardIcon }} ml-auto" aria-label="공유">
                        <x-icon-share class="size-6" />
                    </button>
                </div>

                <div class="mt-[15px] h-px bg-warm-gray-100 lg:mt-5" aria-hidden="true"></div>

                {{-- ── 댓글 ── --}}
                <div class="flex flex-col gap-5 pt-5 lg:gap-[30px] lg:pt-[30px]">
                    @foreach ($comments as $comment)
                        <div class="min-w-0">
                            @include('partials.post-comment', ['comment' => $comment])
                        </div>
                    @endforeach
                </div>

                {{-- ── 댓글 입력 — 아바타가 알약 왼쪽 끝에 겹친다(원본 그대로) ──
                     원본은 알약과 아바타가 둘 다 38 이다. 아바타를 40 으로 올렸으므로
                     알약도 40 으로 맞춘다. 글자 왼쪽 = 아바타(40) + 10. --}}
                <div class="relative mt-5 flex h-10 min-w-0 items-center gap-2.5 rounded-full bg-warm-gray-50 pl-[50px] pr-3 lg:mt-[23px] lg:h-12 lg:pl-[58px] lg:pr-[18px]">
                    <x-thumbnail name="김기안" size="md" shape="circle" class="absolute left-0 top-0 lg:hidden" />
                    <x-thumbnail name="김기안" size="lg" shape="circle" class="absolute left-0 top-0 max-lg:hidden" />

                    <button type="button" class="min-w-0 flex-1 truncate text-left text-label-2 leading-5 text-warm-gray-400 transition-colors hover:text-label-alternative lg:text-body-2 lg:leading-[23px]">
                        댓글을 남겨보세요
                    </button>
                    <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="이모지">
                        <x-icon-face-smile class="size-6" />
                    </button>
                </div>
            </article>
        </div>
    </x-workspace-shell>
</x-layout>
