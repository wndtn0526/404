{{-- 프로필 설정 — Figma GPRO_PORTFOLIO (sJC6AduTG0I4cTttQJFAes · node 1104-58578 "퍼블릭")
     퍼블릭 스페이스의 '프로필 설정' 버튼이 오는 화면.

     원본 실측(1920) — GNB 56 · 본문 1200 가운데 정렬(좌우 여백 각 360)
       제목 '프로필 설정' 26 Bold lh34 -1px (카드 왼쪽 끝과 같은 x) · 제목 아래 20
       좌 카드 792 · 우 카드 384 · 사이 24 · 반경 6 · 내부 패딩 30
       아바타 60(카메라 배지) · 아바타↔이름 12 · 이름 18 Bold lh27 · 직무 12 lh18 Warm gray/500
       아바타 아래 40 · 필드 사이 35 (인풋 바닥 → 다음 라벨)
       마지막 필드 아래 55 · 안내문 13 lh20 Warm gray/600 3줄
       안내문 아래 40 · 구분선 1px Warm gray/100 · 그 아래 30
       버튼 120x43 · 사이 15 · 우측 정렬
       우 카드 패딩 15/18 · 항목 49 · 반경 6 · 활성 배경 Warm gray/050 · 글자 15
       비활성 글자 Warm gray/400

     ⚠️ 원본에는 LNB 가 없다. GNB 좌측이 '펼치기' 화살표라 LNB 를 접은 상태로 보인다.
        LNB 접기·펼치기는 나중에 셸에 넣기로 했다 — 그때 이 화면은 접힌 상태가 기본이 된다.
        지금은 셸 기본(항상 펼침)을 그대로 쓴다.
        원본 GNB 에는 앱 전환 아이콘이 없고 아이콘 사이가 24 다(셸은 32).
     ⚠️ 원본 입력 박스는 높이 46 · 반경 6 · 배경 Warm gray/050 · 본문 16 · 라벨 12 Medium 이다.
        DS 인풋(Figma 1002:518593)은 높이 40 · 반경 4 · 흰 배경 · 본문 14 · 라벨 16 이다.
        폼 컨트롤은 DS 가 정본이라 DS 값을 썼다 — GPRO 쪽 '채운 인풋' 스타일은 들여오지 않았다.
     ⚠️ 원본 관심사 칩에는 삭제(×) 버튼이 있다. DS Input Box Tag 는 칩만 얹는다.
        실제 폼이 붙으면 chip-removable 로 바꾼다.
     ⚠️ 제목 26px 이 DS 단계에 없다(22 · 24 · 30). title-3(24)에 줄높이만 34 로 맞췄다.
     ⚠️ 저장 버튼은 원본이 회색 면 + 회색 글자다. 아직 바꾼 게 없어 못 누르는 상태로 보고
        DS disabled 로 뒀다(면이 Warm gray/100, 원본은 200 — 한 단계 차이).
        취소도 원본 글자색이 Warm gray/400 이지만 눌러야 하는 버튼이라 일반 outline 로 뒀다.
     ⚠️ 아바타는 원본이 스톡 일러스트다. DS 썸네일(이니셜) + 카메라 배지로 뒀다.
        원본 60 인데 DS 단계가 48/64 라 64 를 썼다.
     ⚠️ 원본은 제목이 GNB 바로 밑(y56)에 붙어 있다. 셸 본문의 위 여백 32 를 그대로 뒀다 —
        다른 화면과 시작 높이를 맞추는 쪽을 택했다.
     ⚠️ 모바일 노드가 없다. 두 카드를 위아래로 쌓았다.
     ⚠️ 본문은 예시다. DB 에서 오지 않는다. 저장 동작도 아직 없다. --}}
@php
    $profile = [
        'name' => '신고수',
        'job' => '프로덕트 디자이너',
        'bio' => '스타트업에서 일하고 있는 7년 차 프로덕트 디자이너입니다:)',
        // 원본 칩은 둘 다 '태그' 라는 자리표시자다. 퍼블릭 스페이스의 관심사를 그대로 썼다.
        'interests' => ['프로덕트 디자인', '비즈니스', 'UXUI'],
    ];

    // 우측 메뉴 — 프로필 편집만 열려 있다. 나머지는 화면이 없다(원본도 회색으로 꺼져 있다).
    $menu = [
        ['label' => '프로필 편집', 'current' => true],
        ['label' => '계정 설정', 'current' => false],
        ['label' => '공개 범위 설정', 'current' => false],
    ];

    $notes = [
        '인사 정보가 중점인 워크스페이스 프로필과는 달리 퍼블릭 스페이스의 프로필은 익명을 바탕으로 더 자유롭게 사용할 수 있습니다.',
        '대신 검증된 커리어를 기반으로 소통하기 때문에 워크스페이스에 등록된 회원님의 직무는 수정할 수 없습니다.',
        '내 소개나 회원님만의 관심사 태그를 등록해서 자신을 나타내보세요.',
    ];

    $sectionTitle = 'text-title-3 font-bold leading-[34px] text-mono-black';
@endphp

<x-layout title="프로필 설정">
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
        <div class="mx-auto w-full min-w-0 max-w-[1200px] lg:flex lg:items-start lg:gap-6">

            {{-- ═══ 좌: 792 — 프로필 편집 폼 ═══ --}}
            <div class="min-w-0 flex-1">
                <h1 class="{{ $sectionTitle }}">프로필 설정</h1>

                <form method="POST" action="#" class="mt-5 min-w-0 rounded-lg bg-background-normal p-5 lg:p-[30px]">
                    {{-- 아직 저장 엔드포인트가 없다. 붙일 때 @csrf 를 여기 둔다 —
                         상태를 바꾸는 요청은 POST + CSRF (CLAUDE.md). --}}

                    {{-- ── 프로필 ── --}}
                    <div class="flex min-w-0 items-start gap-3">
                        <button type="button"
                                class="group relative shrink-0 rounded-full focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                aria-label="프로필 사진 변경">
                            {{-- 카메라 — 원본은 아바타 가운데에 흰 카메라 18 만 얹는다.
                                 fallback="none" 이라 이니셜을 그리지 않는다. 흰 글자와 흰 글리프가
                                 겹쳐서 둘 다 안 읽히기 때문이다. 사진이 붙으면 src 를 넘긴다. --}}
                            <x-thumbnail size="xl" shape="circle" fallback="none" class="transition-opacity group-hover:opacity-80" />
                            <span class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                                <x-icon-camera class="size-[18px] text-white" />
                            </span>
                        </button>

                        <div class="min-w-0 pt-1.5">
                            <p class="truncate text-headline-2 font-bold leading-[27px] text-mono-black">{{ $profile['name'] }}</p>
                            <p class="truncate pt-[2px] text-caption-1 leading-[18px] text-warm-gray-500">{{ $profile['job'] }}</p>
                        </div>
                    </div>

                    {{-- ── 필드 ── --}}
                    <div class="mt-10 flex min-w-0 flex-col gap-[35px]">
                        {{-- 글자 수 카운터를 넣지 않는다. 원본 필드에도 하단 보조 줄(sub text)이
                             숨겨져 있다 — 길이 제한이 정해지면 그때 maxlength 를 준다. --}}
                        <x-input label="닉네임" name="nickname" :value="$profile['name']" />

                        <x-textarea label="내 소개" name="bio" rows="3">{{ $profile['bio'] }}</x-textarea>

                        {{-- 원본 Input Box Tag — 박스 안에 칩을 얹고 이어서 입력한다 --}}
                        <x-input label="관심사" name="interests" :tags="$profile['interests']" placeholder="관심사를 입력하세요" />
                    </div>

                    {{-- ── 안내 — 원본이 줄마다 가운뎃점으로 시작한다 ── --}}
                    <ul class="mt-[55px] flex min-w-0 flex-col">
                        @foreach ($notes as $note)
                            <li class="flex min-w-0 text-label-2 leading-5 text-warm-gray-600">
                                <span class="shrink-0 pr-1" aria-hidden="true">·</span>
                                <span class="min-w-0">{{ $note }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-10 h-px bg-warm-gray-100" aria-hidden="true"></div>

                    <div class="mt-[30px] flex flex-wrap items-center justify-end gap-[15px]">
                        <x-button variant="outline" size="sm" href="{{ url('/public-space') }}" class="w-[120px]">취소</x-button>
                        {{-- 바꾼 게 없으면 저장할 것도 없다. 원본도 꺼져 있다. --}}
                        <x-button variant="primary" size="sm" type="submit" class="w-[120px]" disabled>저장</x-button>
                    </div>
                </form>
            </div>

            {{-- ═══ 우: 384 — 설정 메뉴 ═══ --}}
            <div class="mt-8 min-w-0 lg:mt-0 lg:w-96 lg:shrink-0">
                <h2 class="{{ $sectionTitle }}">메뉴</h2>

                <nav class="mt-5 rounded-lg bg-background-normal px-[15px] py-[18px]" aria-label="설정 메뉴">
                    <ul>
                        @foreach ($menu as $item)
                            <li>
                                @if ($item['current'])
                                    <span aria-current="page"
                                          class="flex h-[49px] items-center rounded-lg bg-warm-gray-50 px-[15px] text-body-2 leading-[23px] text-mono-black">
                                        {{ $item['label'] }}
                                    </span>
                                @else
                                    {{-- 해당 화면이 아직 없다. 원본도 회색으로 꺼져 있어 링크로 내지 않는다. --}}
                                    <span aria-disabled="true"
                                          class="flex h-[49px] items-center rounded-lg px-[15px] text-body-2 leading-[23px] text-warm-gray-400">
                                        {{ $item['label'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    </x-workspace-shell>
</x-layout>
