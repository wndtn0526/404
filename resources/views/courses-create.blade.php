{{-- 과정 추가 — 컨텐츠 관리에 올라간 컨텐츠를 골라 하나의 과정으로 묶는다.
     레이아웃은 컨텐츠 추가와 같다 (Figma GPRO_PORTFOLIO node 1002-269747 · 가운데 792 카드 ·
     섹션 제목 + 2열 그리드 · 하단 구분선 + 취소/추가).

     컨텐츠 추가와 다른 건 가운데 '컨텐츠 구성' 절이다. 이 화면의 핵심이라 두 열을 다 쓰고,
     담은 컨텐츠에서 차시와 총 재생시간을 그 자리에서 계산해 보여준다.
     둘 다 사람이 입력하는 값이 아니라 묶은 결과다 — 손입력 칸으로 두면 표의 값과 어긋난다.

     컨텐츠는 '컨텐츠 추가' 버튼 → 모달에서 고른다. 모달 레이아웃은
     Figma GPRO_PORTFOLIO node 1002-267385 실측이다.

     ⚠️ 이 화면은 Figma 에 디자인이 없다. 레이아웃만 컨텐츠 추가에서 가져왔다.
     ⚠️ 차시 순서는 컨텐츠 목록에 놓인 순서다(담은 순서가 아니다). 순서를 바꾸는 UI(끌어서
        정렬)는 없다. 차시 번호가 곧 커리큘럼 순서라 실제로는 필요하다 — 디자인이 나오면 붙인다.
     ⚠️ 모달 안 필터 바는 정적이다. 목록을 실제로 걸러내려면 서버 조회가 붙어야 한다.
     ⚠️ 저장 엔드포인트가 없다. '추가'는 type=button 이라 아무 일도 하지 않는다.
        붙으면 type=submit 으로 바꾸고 form 에 @csrf 를 넣는다(상태를 바꾸는 요청은 POST + CSRF).
     ⚠️ 고를 수 있는 컨텐츠 목록은 뷰에 박아둔 예시다. 실제로는 컨텐츠 테이블에서 와야 한다.
     ⚠️ 환급/비환급 같은 교육 유형 구분은 넣지 않았다. 규정(고용노동부·산업인력공단)이
        걸리는 값이라 담당자 확인 없이 화면에 세우지 않는다. --}}
@php
    $major = ['요양보호' => '요양보호', '방문간호' => '방문간호', '치매전문' => '치매전문', '공통' => '공통'];
    $minor = ['직무향상' => '직무향상', '기록관리' => '기록관리', '의사소통' => '의사소통', '안전관리' => '안전관리', '인권보호' => '인권보호'];
    $archive = ['법정의무교육' => '법정의무교육', '전문교육' => '전문교육', '실무자료' => '실무자료'];
    $states = ['공개' => '공개', '검수중' => '검수중', '비공개' => '비공개', '반려' => '반려'];

    /*
     * 고를 수 있는 컨텐츠. sec 은 영상 길이(초)다 — 컨텐츠 관리 표의 '영상 분·초' 를 초로 합친 값.
     * 영상이 없는 자료(실무자료 PDF 등)는 sec 이 null 이고 재생시간이 하이픈으로 나온다.
     * 과정에 넣을 수는 있다 — 차시에는 세고 재생시간에는 더하지 않는다.
     */
    $contents = [
        ['id' => 'C-1042', 'title' => '요양보호사 직무향상 1차시 · 감염관리', 'major' => '요양보호', 'minor' => '직무향상', 'sec' => 1450],
        ['id' => 'C-1041', 'title' => '방문간호 기록지 작성 가이드', 'major' => '방문간호', 'minor' => '기록관리', 'sec' => null],
        ['id' => 'C-1040', 'title' => '치매전문교육 2차시 · 의사소통', 'major' => '치매전문', 'minor' => '의사소통', 'sec' => 1905],
        ['id' => 'C-1039', 'title' => '안전사고 예방 체크리스트', 'major' => '공통', 'minor' => '안전관리', 'sec' => null],
        ['id' => 'C-1038', 'title' => '노인학대 예방 교육 · 사례 중심', 'major' => '공통', 'minor' => '인권보호', 'sec' => 1082],
    ];

    // 표기 — 값이 없으면 하이픈. em dash 는 쓰지 않는다.
    $runtime = fn (?int $sec) => $sec === null || $sec <= 0
        ? '-'
        : str_pad((string) intdiv($sec, 60), 2, '0', STR_PAD_LEFT).'분 '
            .str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT).'초';

    $grid = 'grid grid-cols-1 gap-x-[30px] gap-y-6 sm:grid-cols-2';
@endphp

<x-layout title="과정 추가">
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
        <div class="mx-auto w-full min-w-0 max-w-[792px]">

            <x-breadcrumb :items="[
                ['label' => '홈', 'href' => url('/workspace')],
                ['label' => '과정 관리', 'href' => url('/courses')],
                ['label' => '과정 추가'],
            ]" />

            <div class="flex min-w-0 items-center gap-4 pt-[30px]">
                <a href="{{ url('/courses') }}"
                   class="inline-flex size-8 shrink-0 items-center justify-center rounded-md text-label-normal transition-colors hover:bg-warm-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                   aria-label="과정 관리로 돌아가기">
                    <x-icon-arrow-left class="size-6" />
                </a>
                <h1 class="min-w-0 truncate text-title-2 font-bold leading-[39px] text-mono-black">과정 추가</h1>
            </div>

            <form method="POST" action="#" class="mt-[30px] min-w-0 rounded-lg bg-background-normal p-5 lg:p-[30px]">
                {{-- 엔드포인트가 붙을 때 @csrf 를 여기 둔다. --}}

                {{-- ═══ 기본 설정 (필수) ═══
                     필드마다 required 를 걸지 않는다. 필수는 섹션 제목에서 한 번만 알린다.
                     검증 규칙은 저장이 붙을 때 FormRequest·서비스에 둔다. --}}
                <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">기본 설정 (필수)</h2>

                <div class="{{ $grid }} pt-[30px]">
                    <x-input label="과정명" name="title" size="sm" placeholder="과정명 입력" />
                    <x-dropdown label="상태" name="state" size="sm" :options="$states" selected="검수중" />

                    <x-dropdown label="대분류" name="major" size="sm" :options="$major" placeholder="대분류 선택" />
                    <x-dropdown label="중분류" name="minor" size="sm" :options="$minor" placeholder="중분류 선택" />

                    <x-dropdown label="아카이브 분류" name="archive" size="sm" :options="$archive" placeholder="아카이브 분류 선택" />
                    <x-input label="태그명" name="tags" size="sm" placeholder="쉼표로 구분해서 입력" />
                </div>

                <div class="mt-[30px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- ═══ 컨텐츠 구성 ═══ 이 화면의 핵심.
                     '컨텐츠 추가' 를 누르면 모달에서 고르고, 확정한 것만 아래 표에 차시로 쌓인다.

                     상태를 이 x-data 하나가 들고 있다:
                       picked   확정된 컨텐츠 id (표에 보이는 차시 목록)
                       selected 모달에서 고르는 중인 id
                     ⚠️ 'selected' 라는 이름은 임의로 고른 게 아니다. DS <x-table.row selectable>
                        의 체크박스가 x-model="selected" 로 이 이름을 찾는다. 그래서 <x-table>
                        에는 selectable 을 주지 않는다 — 주면 표가 자기 x-data 로 이 이름을
                        덮어써서 모달 밖에서 값을 읽지 못한다.
                     ⚠️ 계산은 전부 메서드로 둔다. x-text 안에 화살표 함수를 직접 쓰면 Alpine
                        평가기에서 값이 falsy 로 떨어지는 걸 봤다. --}}
                <div x-data="{
                        catalog: @js($contents),
                        picked: [],
                        selected: [],
                        openPicker() { this.selected = [...this.picked]; },
                        apply() { this.picked = this.catalog.map(c => c.id).filter(id => this.selected.includes(id)); },
                        rows() { return this.catalog.filter(c => this.picked.includes(c.id)); },
                        remove(id) { this.picked = this.picked.filter(x => x !== id); },
                        total() { return this.rows().reduce((sum, c) => sum + (c.sec || 0), 0); },
                        silentCount() { return this.rows().reduce((n, c) => n + (c.sec ? 0 : 1), 0); },
                        fmt(s) {
                            if (! s) return '-';
                            const m = Math.floor(s / 60), r = s % 60;
                            return String(m).padStart(2, '0') + '분 ' + String(r).padStart(2, '0') + '초';
                        },
                    }">

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-[30px]">
                        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">컨텐츠 구성</h2>
                        <x-button variant="outline" size="sm" icon="plus"
                                  @click="openPicker(); $dispatch('open-modal', 'content-picker')">
                            컨텐츠 추가
                        </x-button>
                    </div>
                    <p class="pt-2 text-label-2 leading-5 text-warm-gray-600">
                        담은 순서가 아니라 컨텐츠 목록에 놓인 순서가 차시 번호가 됩니다.
                    </p>

                    {{-- 담긴 컨텐츠 — Alpine 이 그린다. 서버가 렌더한 <tr> 하나를 x-for 가 복제한다. --}}
                    <x-table min-width="620px" class="mt-[18px]">
                        <x-table.head :columns="[
                            ['label' => '차시', 'width' => '90px'],
                            ['label' => '컨텐츠ID', 'width' => '110px'],
                            ['label' => '제목'],
                            ['label' => '재생시간', 'align' => 'right', 'width' => '120px'],
                            ['label' => '', 'align' => 'right', 'width' => '80px'],
                        ]" />
                        <tbody>
                            <template x-for="(c, i) in rows()" :key="c.id">
                                <x-table.row :hover="false">
                                    <x-table.cell tone="muted" nowrap>
                                        <span class="tabular-nums" x-text="(i + 1) + '차시'"></span>
                                    </x-table.cell>
                                    <x-table.cell tone="muted" nowrap>
                                        <span class="text-label-2 tabular-nums" x-text="c.id"></span>
                                    </x-table.cell>
                                    <x-table.cell tone="strong"><span x-text="c.title"></span></x-table.cell>
                                    <x-table.cell align="right" tone="muted" nowrap>
                                        <span class="tabular-nums" x-text="fmt(c.sec)"></span>
                                    </x-table.cell>
                                    <x-table.cell align="right" nowrap>
                                        <button type="button" @click="remove(c.id)"
                                                class="text-label-2 font-bold text-warm-gray-600 transition-colors hover:text-status-negative focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                                                :aria-label="c.title + ' 빼기'">
                                            빼기
                                        </button>
                                    </x-table.cell>
                                </x-table.row>
                            </template>

                            {{-- 비었을 때. x-if 는 노드를 넣고 빼는 방식이라 x-show 와 달리 확실히 돈다. --}}
                            <template x-if="picked.length === 0">
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-body-2 text-warm-gray-500">
                                        아직 담긴 컨텐츠가 없습니다. 오른쪽 위 ‘컨텐츠 추가’ 로 골라 주세요.
                                    </td>
                                </tr>
                            </template>
                        </tbody>

                        {{-- 합계 — 차시는 담은 개수, 총 재생시간은 영상이 있는 것만 더한다. --}}
                        <tfoot class="border-t border-line-solid-normal bg-background-alternative">
                            <tr>
                                <td colspan="5" class="px-5 py-3">
                                    <div class="flex flex-wrap items-baseline gap-x-6 gap-y-1">
                                        <span class="text-label-1 text-label-alternative">
                                            차시
                                            <strong class="pl-1.5 font-bold text-label-normal tabular-nums"
                                                    x-text="picked.length + '차시'">0차시</strong>
                                        </span>
                                        <span class="text-label-1 text-label-alternative">
                                            총 재생시간
                                            <strong class="pl-1.5 font-bold text-label-normal tabular-nums"
                                                    x-text="fmt(total())">-</strong>
                                        </span>
                                        {{-- ⚠️ x-show 가 아니라 :class 로 감춘다. 이 자리에서 x-show 는 값이
                                             true 로 바뀌어도 다시 그려지지 않았다(표현식·스코프는 정상,
                                             같은 자리의 :class 는 반응했다). Alpine 이 show 를
                                             requestAnimationFrame 으로 미루는데 그 프레임에 기대지 않는
                                             쪽을 택했다. 기본 클래스에 hidden 을 넣어 Alpine 이 붙기 전에도,
                                             못 붙어도 감춰진 채로 남는다. --}}
                                        <span class="hidden text-label-2 text-warm-gray-600"
                                              :class="{ 'hidden': silentCount() === 0 }">
                                            영상이 없는 자료는 재생시간에 더하지 않습니다.
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </x-table>

                    {{-- ── 컨텐츠 고르기 모달 ──
                         레이아웃은 Figma GPRO_PORTFOLIO node 1002-267385 실측:
                           패널 840 · 반경 6 · 패딩 30 · 제목 20 Bold lh30 (DS heading-2 와 정확히 일치)
                           본문 아래 카드 폭 구분선 → 25 → 버튼 120x36 (사이 16, 우측 정렬) → 30
                         ⚠️ DS 모달 제목은 heading-1(22)이라 원본 20 보다 두 단계 크다. DS 값을 썼다.
                         ⚠️ 원본 제목 앞 이모지(🗓)는 빼기로 한 규칙대로 넣지 않았다.
                         ⚠️ 필터 바는 /contents · /courses 와 같은 DS 컴포넌트다. 정적 화면이라
                            실제 재조회는 없다 — 목록을 실제로 걸러내려면 서버 조회가 붙어야 한다. --}}
                    <x-modal name="content-picker" title="컨텐츠 추가" max-width="max-w-[840px]" scroll close-button>
                        <x-filter-bar
                            search="컨텐츠ID · 제목 검색"
                            :count="count($contents)"
                            :active="['major']"
                            :columns="[
                                ['key' => 'major', 'label' => '대분류', 'type' => 'select', 'options' => ['요양보호', '방문간호', '치매전문', '공통']],
                                ['key' => 'minor', 'label' => '중분류', 'type' => 'select', 'options' => ['직무향상', '기록관리', '의사소통', '안전관리', '인권보호']],
                                ['key' => 'archive', 'label' => '아카이브 분류', 'type' => 'select', 'options' => ['법정의무교육', '전문교육', '실무자료']],
                                ['key' => 'year', 'label' => '제작연도', 'type' => 'select', 'options' => ['2021', '2020', '2019']],
                            ]"
                            class="pb-3"
                        />

                        {{-- ⚠️ <x-table> 에 selectable 을 주지 않는다. 주면 표가 자기 x-data 로
                             selected 를 만들어 바깥 상태를 덮는다. head·row 에만 준다. --}}
                        <x-table min-width="640px">
                            <x-table.head
                                selectable
                                :all-ids="collect($contents)->pluck('id')->all()"
                                :columns="[
                                    ['label' => '컨텐츠ID', 'width' => '110px'],
                                    ['label' => '제목'],
                                    ['label' => '대분류', 'width' => '100px'],
                                    ['label' => '중분류', 'width' => '100px'],
                                    ['label' => '재생시간', 'align' => 'right', 'width' => '120px'],
                                ]"
                            />
                            <tbody>
                                @foreach ($contents as $c)
                                    <x-table.row selectable :value="$c['id']">
                                        <x-table.cell tone="muted" nowrap>
                                            <span class="text-label-2 tabular-nums">{{ $c['id'] }}</span>
                                        </x-table.cell>
                                        <x-table.cell tone="strong">{{ $c['title'] }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>{{ $c['major'] }}</x-table.cell>
                                        <x-table.cell tone="muted" nowrap>{{ $c['minor'] }}</x-table.cell>
                                        <x-table.cell align="right" tone="muted" nowrap>
                                            <span class="tabular-nums">{{ $runtime($c['sec']) }}</span>
                                        </x-table.cell>
                                    </x-table.row>
                                @endforeach
                            </tbody>
                        </x-table>

                        <x-slot:footer>
                            {{-- DS 모달 푸터는 gap-3 좌측 정렬이다. 원본은 우측 정렬 · 사이 16 이라
                                 안에서 한 번 더 감싼다. --}}
                            <div class="flex w-full flex-wrap items-center justify-end gap-4">
                                <span class="mr-auto text-label-2 text-warm-gray-600">
                                    <span x-text="selected.length">0</span>개 선택
                                </span>
                                <x-button variant="outline" size="sm" class="w-[120px]" @click="open = false">취소</x-button>
                                <x-button variant="primary" size="sm" class="w-[120px]"
                                          @click="apply(); open = false">담기</x-button>
                            </div>
                        </x-slot:footer>
                    </x-modal>
                </div>

                <div class="mt-[30px] h-px bg-warm-gray-100" aria-hidden="true"></div>

                {{-- ═══ 상세 설정 ═══ 저장할 때 서버가 채우는 값들 --}}
                <h2 class="pt-[30px] text-heading-2 font-bold leading-[30px] text-mono-black">상세 설정</h2>

                <div class="{{ $grid }} pt-[30px]">
                    <x-input label="과정ID" size="sm" value="저장하면 자동 발급" disabled />
                    <x-input label="등록자" size="sm" value="김기안" disabled />

                    {{-- 앱 타임존(Asia/Seoul)을 따른다. 정적 배포는 뽑는 순간 날짜가 굳는다. --}}
                    <x-input label="등록일" size="sm" :value="now()->format('Y.m.d')" disabled />
                </div>

                <div class="-mx-5 mt-10 h-px bg-warm-gray-100 lg:-mx-[30px]" aria-hidden="true"></div>

                <div class="flex flex-wrap items-center justify-end gap-4 pt-[25px]">
                    <x-button variant="outline" size="sm" href="{{ url('/courses') }}" class="w-[120px]">취소</x-button>
                    <x-button variant="primary" size="sm" type="button" class="w-[120px]">추가</x-button>
                </div>
            </form>
        </div>
    </x-workspace-shell>
</x-layout>
