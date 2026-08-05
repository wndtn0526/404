{{-- 퍼블릭 스페이스 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1104-55195 "퍼블릭")
     레일의 나침반 심볼이 착지하는 화면. 워크스페이스 밖이라 LNB 메뉴가 다르다
     (홈 · 아티클 · 그룹). 크롬은 <x-workspace-shell> 을 그대로 쓴다.

     원본 실측 — 본문 2컬럼: 피드 690 + 추천 486, 사이 24. LNB(240) 오른쪽 영역에서
                 가운데 정렬되어 좌우 여백이 각 240 이다(1920 기준).
                 카드 반경 6 · 카드 내부 패딩 30 · 프로필 48 · 구분선 Warm gray/100.

     ⚠️ 프로필·아티클 썸네일은 원본이 스톡 사진이다. 저장소가 public 이라 사진을 넣지 않고
        DS 썸네일(이니셜)과 토큰 색 면으로 대신했다. 실제 이미지가 붙으면 교체한다.
     ⚠️ 그룹 썸네일의 Apple·Framer 로고도 타사 상표라 넣지 않고 이니셜로 뒀다.
     ⚠️ 본문은 예시다. DB 에서 오지 않는다. --}}
@php
    /*
     * 프로필 — 잡타이틀 + 이름 조합. PC 는 한 줄로 합치고, 모바일은 이름을 위로 올린다.
     * 두 화면이 같은 값을 쓰므로 여기서 한 번만 정의한다.
     */
    $profile = [
        'name' => '신고수',
        'job' => '프로덕트 디자이너',
        'followers' => '1,036',
        'following' => '388',
        'tags' => '프로덕트 디자인 · 비즈니스 · UXUI',
        'bio' => '스타트업에서 일하고 있는 7년 차 프로덕트 디자이너입니다:)',
    ];

    $feed = [
        [
            'author' => '신고수', 'role' => '프로덕트 디자이너', 'when' => '하루 전',
            'title' => '스냅챗이 플랫폼으로 변화한다.',
            'body' => [
                '앱 안에서 간단하게 사용할 수 있는 스냅 미니를 탑재했다.',
                '스냅챗은 ’10초 내 사라지는 메시지’로 알려진 메신저이며 북미에서 MAU 기준 와츠앱을 제치고 2위의 위치를 점유하고 있다. 사용자 수는 페이스북 메신저보다 적지만 Gen Z의 사용 비율이 높다.',
            ],
            'likes' => '15', 'comments' => '7', 'total' => '5',
            'replies' => [
                ['author' => 'Bluecookie', 'role' => 'iOS 개발자', 'when' => '1분 전', 'text' => '넵! 좋을 거 같네요:)'],
                ['author' => 'Hi.jang', 'role' => '마케터', 'when' => '1분 전',
                    'text' => '여러 툴을 써봤지만 프레이머 쓸 거 아니면 피그마가 가장 합리적인 선택이라고 생각한다. 적응해보고 자세한 후기를 남겨보겠다.'],
            ],
        ],
        [
            'author' => 'Bluecookie', 'role' => 'iOS 개발자', 'when' => '하루 전',
            'title' => '안녕하세요.',
            'body' => [
                '앱 안에서 간단하게 사용할 수 있는 스냅 미니를 탑재했다.',
                '스냅챗은 ’10초 내 사라지는 메시지’로 알려진 메신저이며 북미에서 MAU 기준 와츠앱을 제치고 2위의 위치를 점유하고 있다. 사용자 수는 페이스북 메신저보다 적지만 Gen Z의 사용 비율이 높다.',
            ],
            'likes' => '15', 'comments' => '7', 'total' => '5',
            'replies' => [
                ['author' => 'Anothertime', 'role' => '기획자', 'when' => '1분 전', 'text' => '넵! 좋을 거 같네요:)'],
                ['author' => 'Hi.jang', 'role' => '마케터', 'when' => '1분 전',
                    'text' => '여러 툴을 써봤지만 프레이머 쓸 거 아니면 피그마가 가장 합리적인 선택이라고 생각한다. 적응해보고 자세한 후기를 남겨보겠다.'],
            ],
        ],
    ];

    $articles = [
        ['title' => '컬러 조합, 그라데이션, 영문 폰트 조합',
            'desc' => '앞서 소개했던 컬러 관련 서비스들은 컬러 조합을 제공해주거나 그라데이션을 직접 조합할 수 있는 환...',
            'source' => 'Medium · Jay Yoon', 'tone' => 'bg-warm-gray-800'],
        ['title' => '4560 디자인 하우스',
            'desc' => '앞서 소개했던 컬러 관련 서비스들은 컬러 조합을 제공해주거나 그라데이션을 직접 조합할 수 있는 환경.',
            'source' => '브런치 · Sarah Kim', 'tone' => 'bg-warm-gray-300'],
        ['title' => '이미지 편집, 컬러 CSS 생성',
            'desc' => '앞서 소개했던 컬러 관련 서비스들은 컬러 조합을 제공해주고 그라데이션을 직접 조합.',
            'source' => 'Craftwork Design', 'tone' => 'bg-cool-gray-800'],
    ];

    // 그룹 썸네일 배경 — Tailwind 는 문자열로 훑으므로 완성된 클래스명을 담는다.
    $groups = [
        ['name' => '프로덕트 디자이너 그룹', 'desc' => '서로 정보 공유도 하고 네트워킹합시다!',
            'members' => '673', 'tone' => 'bg-cool-gray-800', 'initial' => 'P'],
        ['name' => '맛집 추천 그룹', 'desc' => '서울 경기 지역 맛집 정보 공유 그룹.',
            'members' => '420', 'tone' => 'bg-deep-blue-800', 'initial' => '맛집'],
        ['name' => '앱등이 모임', 'desc' => '앱등이다 싶으면 바로 가입하세요.',
            'members' => '93', 'tone' => 'bg-mono-black', 'initial' => 'A'],
    ];

    /*
     * 커리어 탭 — Figma node 1104-58476 "퍼블릭" (커리어가 활성인 상태).
     * ⚠️ 로고는 원본이 네이버·워크앤조이·하버드 이미지다. 타사 상표라 넣지 않고
     *    이니셜 타일로 뒀다. 배경은 완성된 클래스명을 담는다(Tailwind 문자열 스캔).
     */
    $career = [
        'basics' => [
            ['label' => '이름', 'value' => '신고수'],
            ['label' => '이메일', 'value' => 'Gosu@gmail.com'],
            ['label' => '연락처', 'value' => '010 3366 9393'],
            ['label' => '국적', 'value' => '대한민국'],
            ['label' => '총 경력', 'value' => '7년'],
        ],
        'jobs' => [
            ['initial' => 'N', 'tone' => 'bg-status-positive', 'title' => '프로덕트 디자이너',
                'org' => '네이버 · 프로덕트 디자인팀', 'period' => '2020년 12월 31일 - 현재 · 10개월'],
            ['initial' => 'G', 'tone' => 'bg-warm-gray-800', 'title' => '프로덕트 디자이너',
                'org' => '워크앤조이 · 프로덕트 디자인팀', 'period' => '2020년 12월 31일 - 2020년 12월 31일 · 1년 10개월'],
        ],
        'schools' => [
            ['initial' => 'HA', 'tone' => 'bg-deep-blue-800', 'title' => '하버드 대학교',
                'org' => '학사 · 디자인학과', 'period' => '2020 - 2020'],
        ],
        'skills' => [
            ['title' => '프로덕트 디자인',
                'desc' => '웹, 앱 프로덕트 디자인 프로젝트 수주 사용자 경험을 기반으로 프로덕트 기획, 개선 작업 기존 프로덕트 유지 보수 및 신규 기능 설계 데이터 베이스의 문제 정의, 가설 검증'],
            ['title' => '인터랙션 디자인',
                'desc' => '모션 그래픽을 기반으로 인터랙션 디자인 가이드 제작 모바일과 웹 프로토타이핑 작업 진행'],
        ],
        'files' => [
            ['title' => '2021 신고수 이력서', 'kind' => 'PDF 파일'],
            ['title' => '2021 신고수 자기소개', 'kind' => 'PDF 파일'],
        ],
        'interests' => ['프로덕트 디자인', '비즈니스', 'UXUI'],
    ];

    $cardIcon = 'inline-flex size-6 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40';
@endphp

<x-layout title="퍼블릭 스페이스">
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
        {{-- 탭 전환 — x-tabs 는 x-modelable="value" 라 x-model 로 부모 상태에 묶인다.
             기본값은 커리어(연결된 PC 노드가 커리어 활성 상태다).
             ⚠️ 모바일 노드(1104-59162)는 피드가 활성이다. 한쪽으로 맞춰야 해서 커리어로 뒀다. --}}
        <div x-data="{ tab: 'career' }" class="min-w-0">

        {{-- ═══ 프로필 헤더 + 탭 ═══
             Figma GPRO_PORTFOLIO node 1104-58466 → 화면은 그 안의 1104-58476 "퍼블릭".
             원본 실측(1200 컨테이너 기준) — 아바타 120 (top 40) · 제목 left 160 top 58
                                            메타 top 118 · 탭 top 216 · 구분선 top 256
                                            프로필 설정 버튼 100x36 · 더보기 24 (우측 정렬)
             ⚠️ 원본 아바타는 일러스트레이션 이미지다. 저장소가 public 이라 DS 썸네일(이니셜)로 뒀다. --}}
        <div class="mx-auto w-full min-w-0 max-w-[1200px]">

            {{-- ── 모바일 (lg 미만) — node 1104-59162 "Mobile iOS"
                 이름이 위 24px Bold · lh 31   → DS title-3 과 정확히 일치
                 잡타이틀이 아래 15px · lh 23 · -0.6px · Warm gray/500 → DS body-2 와 정확히 일치
                 아바타는 우측. 원본 70px 인데 DS 썸네일 단계가 64/120 뿐이라 64 를 썼다. ── --}}
            <div class="min-w-0 lg:hidden">
                <div class="flex min-w-0 items-start justify-between gap-4 pt-5">
                    <div class="min-w-0">
                        <h1 class="truncate text-title-3 font-bold leading-[31px] text-mono-black">{{ $profile['name'] }}</h1>
                        <p class="truncate pt-[7px] text-body-2 leading-[23px] text-warm-gray-500">{{ $profile['job'] }}</p>
                    </div>
                    <x-thumbnail :name="$profile['name']" size="xl" shape="circle" />
                </div>

                {{-- 팔로워·팔로잉 — 원본 12px Medium --}}
                <div class="flex min-w-0 flex-wrap items-baseline gap-4 pt-[30px]">
                    <p class="text-caption-1 font-medium text-mono-black">
                        팔로워 <strong class="font-bold tabular-nums">{{ $profile['followers'] }}</strong>
                    </p>
                    <p class="text-caption-1 font-medium text-mono-black">
                        팔로잉 <strong class="font-bold tabular-nums">{{ $profile['following'] }}</strong>
                    </p>
                </div>

                {{-- 태그·소개 — 원본 13px Regular · lh 20 · -0.2px → DS label-2 와 정확히 일치.
                     ⚠️ PC 노드(1104-58476)에는 이 두 줄이 없다. 모바일에만 있는 게 원본이다. --}}
                <div class="min-w-0 pt-2">
                    <p class="text-label-2 leading-5 text-mono-black">{{ $profile['tags'] }}</p>
                    <p class="text-label-2 leading-5 text-mono-black">{{ $profile['bio'] }}</p>
                </div>

                {{-- 모바일 탭 — 피드 · 커리어 (PC 는 4개다).
                     원본이 화면 폭을 2등분하므로 DS x-tabs 의 block 을 쓴다. --}}
                <div class="mt-8 min-w-0">
                    <x-tabs
                        name="profile_tab_mobile"
                        x-model="tab"
                        :options="['feed' => '피드', 'career' => '커리어']"
                        selected="career"
                        block
                        accent="strong"
                    />
                </div>
            </div>

            {{-- ── PC (lg 이상) — node 1104-58476. 잡타이틀 + 이름을 한 줄로 ── --}}
            <div class="hidden min-w-0 items-start gap-10 pt-10 lg:flex">
                <x-thumbnail :name="$profile['name']" size="2xl" shape="circle" />

                <div class="min-w-0 pt-[18px]">
                    {{-- 원본 36px ExtraBold · lh 46 · tracking -1px.
                         DS display-3 이 36px·-1px 까지 같고 줄높이만 54 라 46 으로 눌렀다. --}}
                    <h1 class="text-display-3 font-extrabold leading-[46px] text-mono-black">
                        {{ $profile['job'] }} {{ $profile['name'] }}
                    </h1>

                    {{-- 팔로워·팔로우 — 원본 16px / lh 24. DS body-1 과 정확히 같다. 숫자만 Bold. --}}
                    <div class="flex flex-wrap items-baseline gap-5 pt-[14px]">
                        <p class="text-body-1 leading-6 text-mono-black">
                            팔로워 <strong class="font-bold tabular-nums">{{ $profile['followers'] }}</strong>
                        </p>
                        <p class="text-body-1 leading-6 text-mono-black">
                            팔로우 <strong class="font-bold tabular-nums">{{ $profile['following'] }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            {{-- 탭 + 우측 액션. 구분선이 1200 전체를 지나가야 해서 x-tabs 를 풀폭으로 두고
                 버튼·더보기를 그 위에 얹었다. x-tabs 는 자기 컨테이너 폭만큼 border-b 를 깐다. --}}
            <div class="relative mt-14 hidden lg:block">
                <x-tabs
                    name="profile_tab"
                    x-model="tab"
                    :options="['posting' => '포스팅', 'career' => '커리어', 'group' => '그룹', 'article' => '아티클']"
                    selected="career"
                    accent="strong"
                    class="pr-[176px]"
                />

                <div class="absolute bottom-2.5 right-0 flex items-center gap-2">
                    <x-button variant="outline" size="sm">프로필 설정</x-button>
                    <button type="button" class="{{ $cardIcon }}" aria-label="더보기">
                        <x-icon-more-horizontal class="size-6" />
                    </button>
                </div>
            </div>
        </div>

        {{-- 원본에는 브레드크럼·타이틀이 없다. 본문이 프로필·탭 아래에서 시작한다. --}}
        {{-- ═══ 포스팅 / 피드 패널 ═══
             모바일에서는 블록으로 둔다. flex 행의 아이템이 되면 min-content 가 밀려 올라와
             카드가 뷰포트를 넘어간다. 블록 자식은 컨테이너 폭을 넘지 못한다. --}}
        <div x-show="tab !== 'career'" x-cloak
             class="mx-auto w-full min-w-0 max-w-[1200px] pt-10 lg:flex lg:items-start lg:gap-6">

            {{-- ═══ 좌: 피드 690 ═══ --}}
            {{-- 모바일에서는 화면 폭을 그대로 쓴다. shrink-0 을 걸면 690 이 강제돼 오버플로가 난다. --}}
            <div class="flex w-full min-w-0 flex-col gap-6 lg:max-w-[690px] lg:shrink-0">

                {{-- 글쓰기 — 원본 h80 · 반경 6 · 프로필 48 · 플레이스홀더 Warm gray/400 --}}
                <div class="flex h-20 min-w-0 items-center gap-2.5 rounded-lg bg-background-normal px-5 lg:px-[30px]">
                    <x-thumbnail name="김기안" size="lg" shape="circle" />
                    <button type="button" class="flex-1 text-left text-body-2 text-warm-gray-400 transition-colors hover:text-label-alternative">
                        새로운 소식을 남겨보세요!
                    </button>
                </div>

                {{-- 커뮤니티 카드 --}}
                @foreach ($feed as $post)
                    <article class="min-w-0 rounded-lg bg-background-normal p-5 lg:p-[30px]">
                        {{-- 작성자 --}}
                        <div class="flex items-start gap-2.5">
                            <x-thumbnail :name="$post['author']" size="lg" shape="circle" />
                            <div class="min-w-0 flex-1 pt-0.5">
                                <p class="truncate text-body-2 font-bold leading-[21px] text-mono-black">{{ $post['author'] }}</p>
                                <p class="truncate pt-[5px] text-label-2 leading-5 text-warm-gray-500">
                                    {{ $post['role'] }} · {{ $post['when'] }}
                                </p>
                            </div>
                            <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="더보기">
                                <x-icon-more-horizontal class="size-6" />
                            </button>
                        </div>

                        {{-- 제목 · 본문 — 원본 18 Bold / 15 Regular --}}
                        <h2 class="pt-[30px] text-headline-2 font-bold leading-[27px] text-mono-black">{{ $post['title'] }}</h2>
                        <div class="flex flex-col pt-[12px]">
                            @foreach ($post['body'] as $para)
                                <p class="text-body-2 leading-[23px] text-mono-black">{{ $para }}</p>
                            @endforeach
                        </div>

                        {{-- 반응 — 원본 좋아요·댓글 좌측, 공유 우측 --}}
                        <div class="flex items-center gap-2 pt-[36px]">
                            <button type="button" class="{{ $cardIcon }}" aria-label="좋아요">
                                <x-icon-heart class="size-6 text-status-negative" />
                            </button>
                            <span class="text-body-2 font-bold leading-[21px] text-mono-black tabular-nums">{{ $post['likes'] }}</span>
                            <button type="button" class="{{ $cardIcon }} ml-2.5" aria-label="댓글">
                                <x-icon-bubble class="size-6" />
                            </button>
                            <span class="text-body-2 font-bold leading-[21px] text-mono-black tabular-nums">{{ $post['comments'] }}</span>
                            <button type="button" class="{{ $cardIcon }} ml-auto" aria-label="공유">
                                <x-icon-share class="size-6" />
                            </button>
                        </div>

                        {{-- 구분선 — 원본 Warm gray/100 1px --}}
                        <div class="mt-[20px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                        {{-- 댓글 --}}
                        @foreach ($post['replies'] as $reply)
                            <div class="flex items-start gap-2.5 pt-[30px]">
                                <x-thumbnail :name="$reply['author']" size="lg" shape="circle" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-baseline gap-2">
                                        <p class="text-body-2 font-bold leading-[21px] text-mono-black">{{ $reply['author'] }}</p>
                                        <p class="text-label-2 leading-5 text-warm-gray-600">{{ $reply['role'] }}</p>
                                    </div>
                                    <p class="pt-[8px] text-body-2 leading-[23px] text-mono-black">{{ $reply['text'] }}</p>
                                    <div class="flex items-center gap-2.5 pt-[9px]">
                                        <span class="text-label-2 leading-5 text-warm-gray-600">{{ $reply['when'] }}</span>
                                        <button type="button" class="text-label-2 font-bold leading-5 text-warm-gray-600 transition-colors hover:text-label-normal">
                                            답글 달기
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- 전체 댓글 --}}
                        <button type="button" class="flex items-center gap-1.5 pt-[30px] text-body-2 leading-[23px] text-warm-gray-600 transition-colors hover:text-label-normal">
                            <x-icon-arrow-turn-down-right class="size-[18px] shrink-0" />
                            <span>댓글 {{ $post['total'] }}개 모두 보기</span>
                        </button>

                        {{-- 댓글 입력 — 원본 h48 · 완전 라운드 · BG Warm gray/050 --}}
                        <div class="mt-[23px] flex items-center gap-2.5">
                            <x-thumbnail name="김기안" size="lg" shape="circle" />
                            <div class="flex h-12 min-w-0 flex-1 items-center gap-2.5 rounded-full bg-warm-gray-50 px-[18px]">
                                <button type="button" class="flex-1 text-left text-body-2 text-warm-gray-400 transition-colors hover:text-label-alternative">
                                    댓글을 남겨보세요
                                </button>
                                <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="이모지">
                                    <x-icon-face-smile class="size-6" />
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- ═══ 우: 추천 486 ═══ --}}
            <aside class="hidden w-[486px] shrink-0 flex-col gap-6 xl:flex">

                {{-- 오늘의 아티클 추천 --}}
                <section class="rounded-lg bg-background-normal p-[30px]">
                    <div class="flex items-baseline justify-between">
                        <h2 class="text-headline-2 font-bold leading-[27px] text-mono-black">오늘의 아티클 추천</h2>
                        <a href="#" class="text-body-2 leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">더보기</a>
                    </div>

                    <div class="flex flex-col gap-[40px] pt-[37px]">
                        @foreach ($articles as $article)
                            <a href="#" class="flex items-start gap-5 transition-opacity hover:opacity-70">
                                {{-- 썸네일 120 — 원본은 스톡 스크린샷. 토큰 색 면으로 대신했다. --}}
                                <span class="size-[120px] shrink-0 rounded-md {{ $article['tone'] }}" aria-hidden="true"></span>
                                <span class="flex min-w-0 flex-1 flex-col">
                                    <span class="text-body-2 font-bold leading-[23px] text-mono-black">{{ $article['title'] }}</span>
                                    <span class="pt-[10px] text-body-2 leading-[23px] text-mono-black">{{ $article['desc'] }}</span>
                                    <span class="flex items-center gap-1.5 pt-[14px]">
                                        <span class="size-5 shrink-0 rounded-full bg-warm-gray-200" aria-hidden="true"></span>
                                        <span class="truncate text-label-2 font-medium leading-5 text-warm-gray-500">{{ $article['source'] }}</span>
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>

                {{-- 이런 그룹 어때요? --}}
                <section class="rounded-lg bg-background-normal p-[30px]">
                    <div class="flex items-baseline justify-between">
                        <h2 class="text-headline-2 font-bold leading-[27px] text-mono-black">이런 그룹 어때요?</h2>
                        <a href="#" class="text-body-2 leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">더보기</a>
                    </div>

                    <ul class="pt-[37px]">
                        @foreach ($groups as $i => $group)
                            <li @class(['flex items-start gap-5 py-[24px]', 'border-t border-warm-gray-100' => $i > 0, 'pt-0' => $i === 0])>
                                {{-- 썸네일 48 — 원본은 Apple·Framer 로고. 타사 상표라 이니셜로 뒀다. --}}
                                <span class="flex size-12 shrink-0 items-center justify-center rounded-md {{ $group['tone'] }} text-caption-1 font-bold text-white" aria-hidden="true">
                                    {{ $group['initial'] }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $group['name'] }}</p>
                                    <p class="truncate pt-[5px] text-body-2 leading-[23px] text-mono-black">{{ $group['desc'] }}</p>
                                    <p class="pt-[5px] text-label-2 leading-5 text-warm-gray-500">
                                        <span class="tabular-nums">{{ $group['members'] }}</span>명 참여중
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </aside>
        </div>

        {{-- ═══ 커리어 패널 ═══ Figma node 1104-58476 (커리어 활성 상태)
             좌 687: 기본 정보 · 경력과 학력 · 업무와 스킬 · 추가 서류
             우 486: 소개 · 관심사 · 오늘의 아티클 추천

             원본 실측 — 카드 반경 6 · 내부 좌우 패딩 30 · 섹션 제목 20px Bold · lh 30 · -1px
                         (DS heading-2 와 정확히 일치) · 제목 영역 높이 56
                         기본 정보 행 높이 48 (pt 13 · pb 12) · 라벨 폭 113 · 라벨/값 15px Bold
                         (DS body-2 와 정확히 일치) · 라벨색 Warm gray/500 --}}
        <div x-show="tab === 'career'"
             class="mx-auto w-full min-w-0 max-w-[1200px] pt-10 lg:flex lg:items-start lg:gap-6">

            {{-- ── 좌: 687 ── --}}
            <div class="flex w-full min-w-0 flex-col gap-6 lg:max-w-[690px] lg:shrink-0">

                {{-- 기본 정보 --}}
                <section class="min-w-0 rounded-lg bg-background-normal">
                    <div class="flex items-center justify-between px-5 pb-2 pt-[17px] lg:px-[30px]">
                        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">기본 정보</h2>
                        <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="더보기">
                            <x-icon-more-horizontal class="size-6" />
                        </button>
                    </div>
                    <dl class="pb-[17px]">
                        @foreach ($career['basics'] as $row)
                            <div class="flex items-start px-5 lg:px-[30px]">
                                <dt class="w-[113px] shrink-0 pb-3 pr-2.5 pt-[13px] text-body-2 font-bold leading-[23px] text-warm-gray-500">
                                    {{ $row['label'] }}
                                </dt>
                                <dd class="min-w-0 pb-3 pt-[13px] text-body-2 font-bold leading-[23px] text-mono-black">
                                    {{ $row['value'] }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- 경력과 학력 — 경력 목록 뒤에 구분선을 두고 학력이 온다 --}}
                <section class="min-w-0 rounded-lg bg-background-normal pb-[30px]">
                    <div class="flex items-center justify-between px-5 pb-2 pt-[17px] lg:px-[30px]">
                        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">경력과 학력</h2>
                        <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="더보기">
                            <x-icon-more-horizontal class="size-6" />
                        </button>
                    </div>

                    <ul class="flex flex-col gap-5 px-5 pt-3 lg:px-[30px]">
                        @foreach ($career['jobs'] as $job)
                            @include('partials.career-entry', ['entry' => $job])
                        @endforeach
                    </ul>

                    <div class="mx-5 mt-[26px] h-px bg-warm-gray-100 lg:mx-[30px]" aria-hidden="true"></div>

                    <ul class="flex flex-col gap-5 px-5 pt-[26px] lg:px-[30px]">
                        @foreach ($career['schools'] as $school)
                            @include('partials.career-entry', ['entry' => $school])
                        @endforeach
                    </ul>
                </section>

                {{-- 업무와 스킬 --}}
                <section class="min-w-0 rounded-lg bg-background-normal pb-[30px]">
                    <div class="flex items-center justify-between px-5 pb-2 pt-[17px] lg:px-[30px]">
                        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">업무와 스킬</h2>
                        <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="더보기">
                            <x-icon-more-horizontal class="size-6" />
                        </button>
                    </div>

                    <div class="flex flex-col gap-[26px] px-5 pt-3 lg:px-[30px]">
                        @foreach ($career['skills'] as $skill)
                            <div class="min-w-0">
                                <h3 class="text-body-2 font-bold leading-[23px] text-mono-black">{{ $skill['title'] }}</h3>
                                <p class="pt-2 text-body-2 leading-[23px] text-mono-black">{{ $skill['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- 추가 서류 --}}
                <section class="min-w-0 rounded-lg bg-background-normal pb-[30px]">
                    <div class="flex items-center justify-between px-5 pb-2 pt-[17px] lg:px-[30px]">
                        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">추가 서류</h2>
                        <button type="button" class="{{ $cardIcon }} shrink-0" aria-label="더보기">
                            <x-icon-more-horizontal class="size-6" />
                        </button>
                    </div>

                    <ul class="flex flex-col gap-4 px-5 pt-3 lg:px-[30px]">
                        @foreach ($career['files'] as $file)
                            <li class="flex min-w-0 items-center gap-4">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $file['title'] }}</p>
                                    <p class="truncate pt-[3px] text-label-2 leading-5 text-warm-gray-500">{{ $file['kind'] }}</p>
                                </div>
                                <x-button variant="primary" size="sm" class="shrink-0">다운로드</x-button>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            {{-- ── 우: 486 ── --}}
            <aside class="hidden w-[486px] shrink-0 flex-col gap-6 xl:flex">

                {{-- 소개 --}}
                <section class="rounded-lg bg-background-normal px-[30px] pb-[30px] pt-[17px]">
                    <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">소개</h2>
                    <p class="pt-[26px] text-body-2 leading-[23px] text-mono-black">{{ $profile['bio'] }}</p>
                    <p class="text-body-2 leading-[23px] text-mono-black">유용한 정보들 함께 공유해요!</p>
                </section>

                {{-- 관심사 --}}
                <section class="rounded-lg bg-background-normal px-[30px] pb-[30px] pt-[17px]">
                    <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">관심사</h2>
                    <div class="flex flex-wrap gap-2 pt-[26px]">
                        @foreach ($career['interests'] as $interest)
                            <x-chip size="medium">{{ $interest }}</x-chip>
                        @endforeach
                    </div>
                </section>

                {{-- 오늘의 아티클 추천 — 피드 탭과 같은 카드다 --}}
                <section class="rounded-lg bg-background-normal p-[30px]">
                    <div class="flex items-baseline justify-between">
                        <h2 class="text-headline-2 font-bold leading-[27px] text-mono-black">오늘의 아티클 추천</h2>
                        <a href="#" class="text-body-2 leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">더보기</a>
                    </div>

                    <div class="flex flex-col gap-[40px] pt-[37px]">
                        @foreach ($articles as $article)
                            <a href="#" class="flex items-start gap-5 transition-opacity hover:opacity-70">
                                <span class="size-[120px] shrink-0 rounded-md {{ $article['tone'] }}" aria-hidden="true"></span>
                                <span class="flex min-w-0 flex-1 flex-col">
                                    <span class="text-body-2 font-bold leading-[23px] text-mono-black">{{ $article['title'] }}</span>
                                    <span class="pt-[10px] text-body-2 leading-[23px] text-mono-black">{{ $article['desc'] }}</span>
                                    <span class="flex items-center gap-1.5 pt-[14px]">
                                        <span class="size-5 shrink-0 rounded-full bg-warm-gray-200" aria-hidden="true"></span>
                                        <span class="truncate text-label-2 font-medium leading-5 text-warm-gray-500">{{ $article['source'] }}</span>
                                    </span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>

        </div>{{-- /x-data tab --}}
    
    </x-workspace-shell>
</x-layout>
