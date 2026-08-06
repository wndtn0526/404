{{-- 과정 상세 — 과정 관리 표에서 행을 누르면 오는 화면.
     필드 구조는 컨텐츠 상세와 같다(Figma GPRO_PORTFOLIO node 1002-275959 실측).
       섹션 제목 20 Bold lh30 (DS heading-2) · 소그룹 라벨 14 Bold (DS label-1)
       라벨 14 Medium Warm gray/500 · 값 14 Medium 검정 (둘 다 DS label-1)
       라벨 94 · 라벨↔값 16 · 두 열 사이 24 · 행 피치 36

     탭은 상세 정보 / 컨텐츠 구성 두 개다. 컨텐츠 구성은 이 과정에 묶인 컨텐츠를 차시 순서로
     보여준다 — 컨텐츠 상세의 '과정 편성' 탭과 서로 반대 방향이다.

     ⚠️ 차시와 총 재생시간은 묶인 컨텐츠에서 나온 값이다. 아래 $lessons 로 계산하므로
        표에 적힌 값과 어긋날 수 없다. 과정 관리 목록의 CO-2104 행도 같은 값으로 맞춰 뒀다.
     ⚠️ 정적 화면이라 과정 하나만 보여준다. 과정 관리 표의 모든 행이 이 화면으로 온다.
        모델이 붙으면 /courses/{id} 로 바꾸고 컨트롤러에서 넘겨받는다(로직은 Service Layer).
     ⚠️ 수정·더보기는 아직 동작하지 않는다. 상태를 바꾸는 요청은 POST + CSRF 로 붙인다.
     ⚠️ 환급/비환급 같은 교육 유형 구분은 넣지 않았다. 규정(고용노동부·산업인력공단)이
        걸리는 값이라 담당자 확인 없이 화면에 세우지 않는다. --}}
@php
    // 묶인 컨텐츠 — 차시 순서. sec 은 영상 길이(초), 없으면 null.
    $lessons = [
        ['id' => 'C-1042', 'title' => '요양보호사 직무향상 1차시 · 감염관리', 'major' => '요양보호', 'minor' => '직무향상', 'sec' => 1450],
        ['id' => 'C-1040', 'title' => '치매전문교육 2차시 · 의사소통', 'major' => '치매전문', 'minor' => '의사소통', 'sec' => 1905],
        ['id' => 'C-1038', 'title' => '노인학대 예방 교육 · 사례 중심', 'major' => '공통', 'minor' => '인권보호', 'sec' => 1082],
    ];

    // 표기 — 값이 없으면 하이픈. em dash 는 쓰지 않는다.
    $runtime = fn (?int $sec) => $sec === null || $sec <= 0
        ? '-'
        : str_pad((string) intdiv($sec, 60), 2, '0', STR_PAD_LEFT).'분 '
            .str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT).'초';

    $totalSec = collect($lessons)->sum(fn ($l) => $l['sec'] ?? 0);

    $course = [
        'id' => 'CO-2104',
        'title' => '요양보호사 직무향상 과정 (2021)',
        'major' => '요양보호',
        'minor' => '직무향상',
        'archive' => '법정의무교육',
        'tags' => '감염관리, 위생, 직무향상',
        'state' => '공개',
        'tone' => 'green',
        'writer' => '김기안',
        'at' => '2021.07.31',
    ];

    $groupLabel = 'text-label-1 font-bold leading-5 text-mono-black';
    $fieldGrid = 'grid grid-cols-1 gap-x-6 gap-y-4 lg:grid-cols-2';
@endphp

<x-layout title="과정 상세">
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
        <div class="mx-auto w-full min-w-0 max-w-[1200px]" x-data="{ tab: 'detail' }">

            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '과정 관리', 'href' => url('/courses')],
                ['label' => '과정 상세'],
            ]" />

            <div class="flex min-w-0 items-center gap-4 pt-12">
                <a href="{{ url('/courses') }}"
                   class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                   aria-label="과정 관리로 돌아가기">
                    <x-icon-arrow-left class="size-6" />
                </a>
                <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">
                    {{ $course['title'] }}
                </h1>
            </div>

            <div class="relative mt-[27px]">
                <x-tabs
                    name="course_detail_tab"
                    x-model="tab"
                    :options="['detail' => '상세 정보', 'lessons' => '컨텐츠 구성']"
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

                <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">1. 과정 정보</h2>

                <p class="{{ $groupLabel }} pt-8">일반</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="과정ID" :value="$course['id']" />
                    <x-detail-field label="상태">
                        <x-badge :color="$course['tone']" size="sm">{{ $course['state'] }}</x-badge>
                    </x-detail-field>

                    <x-detail-field label="과정명" :value="$course['title']" />
                    <x-detail-field label="아카이브 분류" :value="$course['archive']" />

                    <x-detail-field label="대분류" :value="$course['major']" />
                    <x-detail-field label="중분류" :value="$course['minor']" />

                    <x-detail-field label="태그명" :value="$course['tags']" />
                </dl>

                {{-- 차시·총 재생시간은 묶인 컨텐츠에서 계산한다. 손으로 적어 두면 어긋난다. --}}
                <p class="{{ $groupLabel }} pt-8">구성</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="차시" :value="count($lessons).'차시'" />
                    <x-detail-field label="총 재생시간" :value="$runtime($totalSec)" />
                </dl>

                <div class="mt-10 h-px bg-warm-gray-100" aria-hidden="true"></div>

                <h2 class="pt-10 text-heading-2 font-bold leading-[30px] text-mono-black">2. 기록</h2>

                <p class="{{ $groupLabel }} pt-8">날짜 기록 관련</p>
                <dl class="{{ $fieldGrid }} pt-4">
                    <x-detail-field label="등록자" :value="$course['writer']" />
                    <x-detail-field label="등록일" :value="$course['at']" />
                </dl>
            </div>

            {{-- ═══ 컨텐츠 구성 ═══ 묶인 컨텐츠를 차시 순서로 --}}
            <div x-show="tab === 'lessons'" x-cloak class="mt-8 min-w-0">
                <x-table min-width="820px">
                    <x-table.head :columns="[
                        ['label' => '차시', 'width' => '90px'],
                        ['label' => '컨텐츠ID', 'width' => '120px'],
                        ['label' => '제목'],
                        ['label' => '대분류', 'width' => '110px'],
                        ['label' => '중분류', 'width' => '110px'],
                        ['label' => '재생시간', 'align' => 'right', 'width' => '130px'],
                    ]" />
                    <tbody>
                        @forelse ($lessons as $i => $lesson)
                            {{-- 행 전체가 컨텐츠 상세로 가는 링크다(제목 셀 링크를 행 전체로 늘렸다). --}}
                            <x-table.row class="relative">
                                <x-table.cell tone="muted" nowrap>
                                    <span class="tabular-nums">{{ $i + 1 }}차시</span>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>
                                    <span class="text-label-2 tabular-nums">{{ $lesson['id'] }}</span>
                                </x-table.cell>
                                <x-table.cell tone="strong">
                                    <a href="{{ url('/contents/detail') }}"
                                       class="after:absolute after:inset-0 focus:outline-none focus-visible:underline focus-visible:decoration-primary focus-visible:underline-offset-4">
                                        {{ $lesson['title'] }}
                                    </a>
                                </x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $lesson['major'] }}</x-table.cell>
                                <x-table.cell tone="muted" nowrap>{{ $lesson['minor'] }}</x-table.cell>
                                <x-table.cell align="right" tone="muted" nowrap>
                                    <span class="tabular-nums">{{ $runtime($lesson['sec']) }}</span>
                                </x-table.cell>
                            </x-table.row>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-body-2 text-warm-gray-500">
                                    묶인 컨텐츠가 없습니다.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="border-t border-line-solid-normal bg-background-alternative">
                        <tr>
                            <td colspan="6" class="px-5 py-3">
                                <div class="flex flex-wrap items-baseline gap-x-6 gap-y-1">
                                    <span class="text-label-1 text-label-alternative">
                                        차시
                                        <strong class="pl-1.5 font-bold text-label-normal tabular-nums">{{ count($lessons) }}차시</strong>
                                    </span>
                                    <span class="text-label-1 text-label-alternative">
                                        총 재생시간
                                        <strong class="pl-1.5 font-bold text-label-normal tabular-nums">{{ $runtime($totalSec) }}</strong>
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </x-table>
            </div>
        </div>
    </x-workspace-shell>
</x-layout>
