{{-- 컨텐츠 상세 — 컨텐츠 관리 표에서 행을 누르면 오는 화면.
     레이아웃은 Figma GPRO_PORTFOLIO node 1002-275959 "인사 상세정보" 를 참고했다.
     프로필 아바타 영역은 빼고 필드 구조만 가져왔다.

     원본 실측(1920) — 본문 1200 가운데 정렬 · 카드 1200 · 반경 6 · 패딩 30
       브레드크럼 56 → 48 → 뒤로(32) + 제목 → 47 → 탭(32) → 32 → 카드 274
       섹션 제목 '1. 개인 정보' 20 Bold lh30 (DS heading-2 와 정확히 일치)
       섹션 제목 → 소그룹 라벨 32 · 소그룹 라벨 14 Bold (DS label-1)
       소그룹 라벨 → 첫 행 36 · 행 사이 36 (줄 20 + 16)
       라벨 94 · 라벨↔값 16 · 두 열 사이 24 (1140 을 570 씩)
       라벨 14 Medium Warm gray/500 · 값 14 Medium 검정 (둘 다 DS label-1)
       마지막 행 → 구분선 40 → 다음 섹션 제목 40 → 카드 아래 30

     ⚠️ 2절 소그룹 '영상' 은 섹션 제목('2. 영상 정보')과 말이 겹친다. 1절처럼 '일반' 으로
        바꾸거나 절을 셋으로 나누는 방법이 있다 — 지금은 요청받은 두 이름만 고쳐 뒀다.
     ⚠️ 과정 상세(courses-detail)는 아직 '2. 기록 > 날짜 기록 관련' 이다. 두 상세 화면을
        같은 말로 맞추려면 거기도 같이 바꿔야 한다.
     ⚠️ 원본에는 '주민번호 표시' 토글이 있다. 컨텐츠에는 대응이 없고, 주민번호는 이 시스템에
        두지 않는 것이 기본이라 옮기지 않았다.
     ⚠️ 원본 탭 우측은 내보내기·더보기 아이콘이다. 상세 화면에서 실제로 필요한 건 수정이라
        수정 버튼 + 더보기로 바꿨다.
     ⚠️ 정적 화면이라 컨텐츠 하나만 보여준다. 컨텐츠 관리 표의 모든 행이 이 화면으로 온다.
        모델이 붙으면 /contents/{id} 로 바꾸고 컨트롤러에서 넘겨받는다(로직은 Service Layer).
     ⚠️ 영상 파일 이름은 표에 없는 값이라 지어냈다. 실제로는 업로드된 파일명이 온다.
     ⚠️ 수정·삭제·내보내기는 아직 동작하지 않는다. 상태를 바꾸는 요청은 POST + CSRF 로 붙인다. --}}
@php
    // 컨텐츠 관리 표의 첫 행(C-1042)과 같은 값이다.
    $content = [
        'id' => 'C-1042',
        'title' => '요양보호사 직무향상 1차시 · 감염관리',
        'major' => '요양보호',
        'minor' => '직무향상',
        'sub' => '감염관리',
        'archive' => '법정의무교육',
        'tags' => '감염관리, 위생',
        'state' => '공개',
        'tone' => 'green',
        'year' => '2021',
        'runtime' => '24분 10초',
        'file' => '요양보호_직무향상_1차시_감염관리.mp4',
        'size' => '512.4MB',
        'writer' => '김기안',
        'at' => '2021.07.31',
    ];

    // 이 컨텐츠가 들어가 있는 과정. 과정 관리 표의 데이터와 맞춘다.
    $courses = [
        ['id' => 'CO-2104', 'title' => '요양보호사 직무향상 과정 (2021)', 'lesson' => '1차시',
            'state' => '공개', 'tone' => 'green'],
    ];

    // 소그룹 라벨 — 섹션 제목 아래 32, 첫 행까지 36
    $groupLabel = 'text-label-1 font-bold leading-5 text-mono-black';
    // 두 열 · 열 사이 24 · 행 사이 16 (줄 20 을 더하면 원본 피치 36)
    $fieldGrid = 'grid grid-cols-1 gap-x-6 gap-y-4 lg:grid-cols-2';
@endphp

<x-layout title="컨텐츠 상세">
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
        {{-- 브레드크럼·제목을 셸 슬롯이 아니라 본문에 둔다. 원본이 둘 다 카드 왼쪽 끝에
             맞춰져 있어서(가운데 1200 기준), 셸의 페이지 헤더(좌우 80)에 두면 어긋난다. --}}
        <div class="mx-auto w-full min-w-0 max-w-[1200px]" x-data="{ tab: 'detail' }">

            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '컨텐츠 관리', 'href' => url('/contents')],
                ['label' => '컨텐츠 상세'],
            ]" />

            {{-- 뒤로 + 제목. 원본은 여기 프로필 아바타 70 이 있는데 빼기로 했다. --}}
            <div class="flex min-w-0 items-center gap-4 pt-12">
                <a href="{{ url('/contents') }}"
                   class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                   aria-label="컨텐츠 관리로 돌아가기">
                    <x-icon-arrow-left class="size-6" />
                </a>
                <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">
                    {{ $content['title'] }}
                </h1>
            </div>

            {{-- 탭 + 우측 액션. 구분선이 1200 전체를 지나가야 해서 x-tabs 를 풀폭으로 두고
                 버튼·더보기를 그 위에 얹는다(퍼블릭 스페이스 PC 탭과 같은 방법). --}}
            <div class="relative mt-[27px]">
                <x-tabs
                    name="content_detail_tab"
                    x-model="tab"
                    :options="['detail' => '상세 정보', 'courses' => '과정 편성']"
                    selected="detail"
                    accent="strong"
                    class="pr-[140px]"
                />

                <div class="absolute bottom-2 right-0 flex items-center gap-2">
                    <x-button variant="outline" size="sm">수정</x-button>
                    <button type="button"
                            class="inline-flex size-6 shrink-0 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                            aria-label="더보기">
                        <x-icon-more-horizontal class="size-6" />
                    </button>
                </div>
            </div>

            {{-- ═══ 상세 정보 ═══ --}}
            <div x-show="tab === 'detail'"
                 class="mt-8 min-w-0 rounded-lg bg-background-normal p-5 lg:p-[30px]">

                {{-- ── 1. 컨텐츠 정보 ── --}}
                <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">1. 컨텐츠 정보</h2>

                <p class="{{ $groupLabel }} pt-8">일반</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="컨텐츠ID" :value="$content['id']" />
                    <x-detail-field label="상태">
                        <x-badge :color="$content['tone']" size="sm">{{ $content['state'] }}</x-badge>
                    </x-detail-field>

                    <x-detail-field label="제목" :value="$content['title']" />
                    <x-detail-field label="아카이브 분류" :value="$content['archive']" />

                    <x-detail-field label="대분류" :value="$content['major']" />
                    <x-detail-field label="중분류" :value="$content['minor']" />

                    <x-detail-field label="소분류" :value="$content['sub']" />
                    <x-detail-field label="태그명" :value="$content['tags']" />
                </dl>

                {{-- 소그룹 사이 32 --}}
                <p class="{{ $groupLabel }} pt-8">기타</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="제작연도" :value="$content['year']" />
                    <x-detail-field label="재생시간" :value="$content['runtime']" />
                </dl>

                <div class="mt-10 h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- ── 2. 영상 정보 ── --}}
                <h2 class="pt-10 text-heading-2 font-bold leading-[30px] text-mono-black">2. 영상 정보</h2>

                <p class="{{ $groupLabel }} pt-8">영상</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="영상 파일">
                        {{-- 내려받기는 권한 확인 후 스트리밍한다. 공개 디스크에 두지 않는다. --}}
                        <a href="#" class="break-all underline decoration-warm-gray-300 underline-offset-4 transition-colors hover:text-primary">
                            {{ $content['file'] }}
                        </a>
                    </x-detail-field>
                    <x-detail-field label="파일 크기" :value="$content['size']" />
                </dl>

                <p class="{{ $groupLabel }} pt-8">등록 정보</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="등록자" :value="$content['writer']" />
                    <x-detail-field label="등록일" :value="$content['at']" />
                </dl>
            </div>

            {{-- ═══ 과정 편성 ═══ 이 컨텐츠가 어느 과정에 들어가 있는지 --}}
            <div x-show="tab === 'courses'" x-cloak class="mt-8 min-w-0">
                <x-table min-width="720px">
                    <x-table.head :columns="[
                        ['label' => '과정ID', 'width' => '130px'],
                        ['label' => '과정명'],
                        ['label' => '차시', 'align' => 'right', 'width' => '100px'],
                        ['label' => '상태', 'align' => 'center', 'width' => '110px'],
                    ]" />
                    <tbody>
                        @forelse ($courses as $course)
                            <x-table.row>
                                <x-table.cell tone="muted" nowrap>
                                    <span class="text-label-2 tabular-nums">{{ $course['id'] }}</span>
                                </x-table.cell>
                                <x-table.cell tone="strong">
                                    <a href="{{ url('/courses') }}" class="underline decoration-warm-gray-300 underline-offset-4 transition-colors hover:text-primary">
                                        {{ $course['title'] }}
                                    </a>
                                </x-table.cell>
                                <x-table.cell align="right" tone="muted" nowrap>
                                    <span class="tabular-nums">{{ $course['lesson'] }}</span>
                                </x-table.cell>
                                <x-table.cell align="center">
                                    <x-badge :color="$course['tone']" size="sm">{{ $course['state'] }}</x-badge>
                                </x-table.cell>
                            </x-table.row>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-10 text-center text-body-2 text-warm-gray-500">
                                    이 컨텐츠가 편성된 과정이 없습니다.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>
    </x-workspace-shell>
</x-layout>
