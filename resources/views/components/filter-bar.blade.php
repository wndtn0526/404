{{-- DS Filter Bar — 백오피스/마이페이지 데이터 테이블용 「툴바」 (Figma 99:22555)
     청담원 플랫폼 저장소(resources/views/components/filter-bar.blade.php)에서 그대로 옮겼다.
     ⚠️ 반경만 손봤다 — 원본이 팝오버·드롭다운 패널에 쓰던 12px 단계 2곳을 6px 로 내렸다.
        우리 반경 스케일은 2/3/4/6px 네 단계뿐이고 DesignSystemTest 가 스케일 밖 반경을
        막는다. 나머지 단계는 원본 그대로다.
     ⚠️ 원본은 청담원 8px 기준이라 우리 6px 에서는 모서리가 2px 덜 둥글다.
     ★ 표준: 키워드 검색 + 필터 + 「총 N건」을 한 줄로 묶어 테이블 바로 위에 둔다.
       (검색창을 테이블과 분리해 따로 두지 말 것 — 대상이 모호해지고 줄이 낭비됨)
     「필터 추가」드롭다운으로 컬럼을 칩으로 추가 → 칩 클릭 시 타입별 값 설정 팝오버.
     정적 UI: 검색어 입력·칩 추가/제거·값 설정까지. 실제 행 재조회는 화면에서 연동.

     props:
       search  : 지정 시 툴바 선두에 자유 텍스트(키워드) 검색창 노출. placeholder 문자열(null=숨김).
                 모바일=전체 폭, lg=260px. (search-type 컬럼 팝오버용 searchPlaceholder와 별개)
       columns : 필터 가능한 컬럼 정의 배열. 각 항목 ['key'=>, 'label'=>, 'type'=>, 'options'=>?]
                 type = select(선택형 체크박스) | search(검색형 체크박스) | date(날짜 범위) | amount(금액 범위)
                 options = select/search 일 때 선택지 배열
       active  : 처음부터 노출할 칩의 key 배열 (기본 [])
       count   : 우측 「총 N건」 표시값 (null이면 숨김)
       searchPlaceholder : search 타입 컬럼 팝오버 내부 검색 인풋 placeholder (기본 '검색')

     사용(표준 — 검색 통합):
       <x-filter-bar search="제목 내용 검색" :count="3" :active="['status','period']" :columns="[
           ['key'=>'period', 'label'=>'기간',       'type'=>'date'],
           ['key'=>'center', 'label'=>'소속 센터',  'type'=>'search', 'options'=>['청담원 본사','서초 분원']],
           ['key'=>'status', 'label'=>'납부 상태',  'type'=>'select', 'options'=>['납부 완료','연체']],
       ]" /> --}}
@props([
    'columns' => [],
    'active' => [],
    'count' => null,
    'countUnit' => '건',          // 「총 N건」의 단위 (예: 명)
    'searchPlaceholder' => '검색',
    'search' => null,
    'searchName' => null,
    'searchValue' => '',
    'initialValues' => [],       // URL 파라미터로부터 복원할 초기 필터값 ['key' => ['val1','val2']]
    'addable' => true,           // 「필터 추가」 노출 (공개 페이지 등 고정 필터셋이면 false)
    'resettable' => true,        // 「초기화」 노출
])

<div {{ $attributes->class('flex flex-col gap-2.5 lg:flex-row lg:items-center lg:justify-between lg:gap-3') }}
     x-data="{
        columns: @js(array_values($columns)),
        active: @js(array_values($active)),
        values: {},
        keyword: @js($searchValue ?? ''),
        addOpen: false,
        editKey: null,
        popLeft: 0,
        regionProv: '',          // region 타입: 현재 열람 중인 시·도
        query: '',
        presets: ['최근 1개월', '3개월', '6개월', '올해'],
        _initial: @js($initialValues),
        init() {
            this.columns.forEach(c => this.reset(c.key));
            // URL 파라미터로부터 초기값 복원
            Object.entries(this._initial).forEach(([k, v]) => { if (Array.isArray(v) && v.length && this.values[k] !== undefined) this.values[k] = v; });
        },
        col(k) { return this.columns.find(c => c.key === k) },
        isActive(k) { return this.active.includes(k) },
        toggleColumn(k) { this.isActive(k) ? this.remove(k) : (this.active.push(k), this.reset(k)) },
        remove(k) { this.active = this.active.filter(x => x !== k); if (this.editKey === k) this.editKey = null },
        reset(k) {
            const t = this.col(k).type;
            this.values[k] = (t === 'select' || t === 'search' || t === 'region') ? []
                : t === 'date' ? { preset: '', from: '', to: '' }
                : { min: '', max: '' };
        },
        // 초기화 후 그대로 두면 화면(펼쳐진 칩)만 비워질 뿐 실제 목록(서버 GET 필터)은 그대로라
        // 초기값 반영 후 다음 tick(히든 인풋 갱신 완료)에 감싸는 form을 제출해 목록을 다시 불러온다.
        resetAll() {
            this.active = @js(array_values($active)); this.columns.forEach(c => this.reset(c.key)); this.editKey = null; this.addOpen = false;
            this.$nextTick(() => this.$el.closest('form')?.submit());
        },
        openEdit(k, el) {
            this.editKey = this.editKey === k ? null : k;
            this.query = '';
            this.addOpen = false;
            // 팝오버를 뷰포트 안으로 clamp — 가운데 pill이 좌/우로 넘치지 않게 pill 기준 left 오프셋 계산
            if (this.editKey === k && el) {
                const type = this.col(k).type;
                if (type === 'region') this.regionProv = Object.keys(this.col(k).regions)[0];
                const margin = 8;
                const r = el.getBoundingClientRect();
                const w = Math.min(type === 'date' ? 400 : type === 'region' ? 320 : 264, window.innerWidth - margin * 2);
                const desired = Math.max(margin, Math.min(r.left, window.innerWidth - w - margin));
                this.popLeft = desired - r.left;
            }
        },
        toggleOption(k, opt) { const a = this.values[k], i = a.indexOf(opt); i > -1 ? a.splice(i, 1) : a.push(opt) },
        summary(k) {
            const c = this.col(k), v = this.values[k];
            if (c.type === 'select' || c.type === 'search') return !v.length ? '전체' : (v.length === 1 ? v[0] : v[0] + ' 외 ' + (v.length - 1));
            if (c.type === 'region') { const last = s => s.split(' ').pop(); return !v.length ? '전체' : (v.length === 1 ? last(v[0]) : last(v[0]) + ' 외 ' + (v.length - 1)); }
            if (c.type === 'date') return v.preset ? v.preset : (v.from && v.to ? v.from + '~' + v.to : '전체');
            return (v.min || v.max) ? (Number(v.min || 0).toLocaleString() + '~' + (v.max ? Number(v.max).toLocaleString() : '')) : '전체';
        },
     }" x-init="init()">
    <div class="flex flex-wrap items-center gap-2">
        {{-- 선두 키워드 검색(선택) — 검색+필터를 한 툴바로 묶어 테이블 바로 위에 배치 --}}
        @if ($search)
            <div class="relative w-full lg:w-[260px]">
                <x-icon-search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-label-assistive" />
                <input type="search" x-model="keyword" placeholder="{{ $search }}" aria-label="{{ $search }}"
                       @if ($searchName) name="{{ $searchName }}" @endif
                       @keydown.enter.prevent="$el.closest('form')?.submit()"
                       class="h-9 w-full rounded-lg border border-line-solid-normal bg-background-normal pl-9 pr-8 text-label-1 text-label-normal placeholder:text-label-assistive transition-colors focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                <button type="button" x-show="keyword" x-cloak @click="keyword = ''" aria-label="검색어 지우기"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-label-assistive transition-colors hover:text-label-alternative">
                    <x-icon-circle-close class="h-[18px] w-[18px]" />
                </button>
            </div>
        @endif

        {{-- 활성 필터 pill(xs): 회색 테두리 · 라벨 + 값(강조) + 캐럿. 값이 '전체'면 라벨만 · 클릭 → 값 설정 팝오버 --}}
        <template x-for="k in active" :key="k">
            <div class="relative" @click.outside="editKey === k && (editKey = null)">
                    <button type="button" @click="openEdit(k, $event.currentTarget)"
                            class="inline-flex h-7 items-center gap-1 rounded-lg border px-2.5 text-label-2 transition-colors"
                            :class="editKey === k ? 'border-primary/[0.43] bg-primary/5' : 'border-line-solid-normal hover:bg-fill-alternative'">
                        <span :class="editKey === k ? 'text-primary' : 'text-label-alternative'" x-text="col(k).label"></span>
                        <span x-show="summary(k) !== '전체'" class="font-semibold" :class="editKey === k ? 'text-primary' : 'text-label-strong'" x-text="summary(k)"></span>
                        <x-icon-caret-down class="h-3.5 w-3.5 shrink-0 transition-transform duration-200" ::class="editKey === k ? 'text-primary rotate-180' : 'text-label-alternative'" />
                    </button>

                {{-- 값 설정 팝오버 --}}
                <div x-show="editKey === k" x-cloak
                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-full z-30 mt-1.5 max-w-[calc(100vw_-_2rem)] overflow-hidden rounded-lg border border-line-solid-neutral bg-background-normal shadow-elevation-lg"
                     :class="col(k).type === 'date' ? 'w-[400px]' : col(k).type === 'region' ? 'w-[320px]' : 'w-[264px]'"
                     :style="{ left: popLeft + 'px' }">
                    {{-- ① 선택형 / ② 검색형 --}}
                    <template x-if="col(k).type === 'select' || col(k).type === 'search'">
                        <div>
                            <div x-show="col(k).type === 'search'" class="border-b border-line-solid-neutral p-2.5">
                                <div class="relative">
                                    <x-icon-search class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-label-assistive" />
                                    <input type="text" x-model="query" placeholder="{{ $searchPlaceholder }}"
                                           class="h-9 w-full rounded-lg border border-line-solid-normal bg-background-normal pl-8 pr-3 text-label-1 text-label-normal placeholder:text-label-assistive focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                </div>
                            </div>
                            <div class="max-h-[200px] overflow-y-auto py-1">
                                <template x-for="opt in col(k).options.filter(o => !query || o.includes(query))" :key="opt">
                                    <button type="button" @click="toggleOption(k, opt)"
                                            class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-label-1 transition-colors"
                                            :class="values[k].includes(opt) ? 'bg-primary-surface font-medium text-primary' : 'text-label-normal hover:bg-fill-alternative'">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-[1.5px] transition-colors"
                                              :class="values[k].includes(opt) ? 'border-primary bg-primary text-white' : 'border-line-normal-strong'">
                                            <x-icon-check class="h-[90%] w-[90%] [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" x-show="values[k].includes(opt)" />
                                        </span>
                                        <span x-text="opt"></span>
                                    </button>
                                </template>
                                <p x-show="!col(k).options.filter(o => !query || o.includes(query)).length" class="px-3 py-4 text-center text-label-1 text-label-assistive">검색 결과가 없어요</p>
                            </div>
                        </div>
                    </template>

                    {{-- ②-b 지역: 시·도(좌) → 시·군·구 체크(우) 2-depth. 값은 '시도 시군구' 문자열 배열 --}}
                    <template x-if="col(k).type === 'region'">
                        <div class="flex h-[280px]">
                            {{-- 시·도 목록 --}}
                            <div class="w-[104px] shrink-0 overflow-y-auto border-r border-line-solid-neutral bg-background-alternative py-1">
                                <template x-for="prov in Object.keys(col(k).regions)" :key="prov">
                                    <button type="button" @click="regionProv = prov"
                                            class="flex w-full items-center justify-between gap-1 px-3 py-2.5 text-left text-label-1 transition-colors"
                                            :class="regionProv === prov ? 'bg-background-normal font-semibold text-primary' : 'text-label-neutral hover:bg-fill-alternative'">
                                        <span x-text="prov"></span>
                                        <span x-show="values[k].some(s => s.startsWith(prov + ' ') || s === prov)"
                                              class="h-1.5 w-1.5 shrink-0 rounded-full bg-primary"></span>
                                    </button>
                                </template>
                            </div>
                            {{-- 시·군·구 체크 --}}
                            <div class="min-w-0 flex-1 overflow-y-auto py-1">
                                {{-- 하위 구역 없는 시·도(세종 등)는 시·도 자체를 선택 --}}
                                <template x-if="!col(k).regions[regionProv].length">
                                    <button type="button" @click="toggleOption(k, regionProv)"
                                            class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-label-1 transition-colors"
                                            :class="values[k].includes(regionProv) ? 'bg-primary-surface font-medium text-primary' : 'text-label-normal hover:bg-fill-alternative'">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-[1.5px] transition-colors"
                                              :class="values[k].includes(regionProv) ? 'border-primary bg-primary text-white' : 'border-line-normal-strong'">
                                            <x-icon-check class="h-[90%] w-[90%] [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" x-show="values[k].includes(regionProv)" />
                                        </span>
                                        <span x-text="regionProv + ' 전체'"></span>
                                    </button>
                                </template>
                                <template x-for="gu in col(k).regions[regionProv]" :key="regionProv + gu">
                                    <button type="button" @click="toggleOption(k, regionProv + ' ' + gu)"
                                            class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-label-1 transition-colors"
                                            :class="values[k].includes(regionProv + ' ' + gu) ? 'bg-primary-surface font-medium text-primary' : 'text-label-normal hover:bg-fill-alternative'">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-[1.5px] transition-colors"
                                              :class="values[k].includes(regionProv + ' ' + gu) ? 'border-primary bg-primary text-white' : 'border-line-normal-strong'">
                                            <x-icon-check class="h-[90%] w-[90%] [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" x-show="values[k].includes(regionProv + ' ' + gu)" />
                                        </span>
                                        <span x-text="gu"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- ③ 날짜 범위 (DS 캘린더 · 시작/종료 세로 · 날짜 선택 시 프리셋 해제) --}}
                    <template x-if="col(k).type === 'date'">
                        <div class="flex flex-col gap-2.5 p-3"
                             x-effect="if ((values[k].from || values[k].to) && values[k].preset) values[k].preset = ''">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="p in presets" :key="p">
                                    <button type="button" @click="values[k].preset = (values[k].preset === p ? '' : p); values[k].from = ''; values[k].to = ''"
                                            class="rounded-lg border px-2.5 py-1.5 text-caption-1 font-medium transition-colors"
                                            :class="values[k].preset === p ? 'border-primary/[0.43] bg-primary/5 text-primary' : 'border-line-normal-neutral text-label-alternative hover:bg-fill-alternative'"
                                            x-text="p"></button>
                                </template>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="min-w-0 flex-1"><x-datepicker size="sm" placeholder="시작일" x-model="values[k].from" /></div>
                                <span class="shrink-0 text-label-assistive">~</span>
                                <div class="min-w-0 flex-1"><x-datepicker size="sm" placeholder="종료일" x-model="values[k].to" /></div>
                            </div>
                        </div>
                    </template>

                    {{-- 금액 범위 --}}
                    <template x-if="col(k).type === 'amount'">
                        <div class="flex items-center gap-1.5 p-3">
                            <div class="relative min-w-0 flex-1">
                                <input type="number" x-model="values[k].min" placeholder="최소"
                                       class="h-9 w-full rounded-lg border border-line-solid-normal bg-background-normal pl-2.5 pr-6 text-label-1 text-label-normal placeholder:text-label-assistive focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-caption-1 text-label-assistive">원</span>
                            </div>
                            <span class="shrink-0 text-label-assistive">~</span>
                            <div class="relative min-w-0 flex-1">
                                <input type="number" x-model="values[k].max" placeholder="최대"
                                       class="h-9 w-full rounded-lg border border-line-solid-normal bg-background-normal pl-2.5 pr-6 text-label-1 text-label-normal placeholder:text-label-assistive focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-caption-1 text-label-assistive">원</span>
                            </div>
                        </div>
                    </template>

                    {{-- 푸터: 초기화 / 적용 --}}
                    <div class="flex items-center justify-between border-t border-line-solid-neutral px-3 py-2.5">
                        <button type="button" @click="reset(k)" class="text-label-1 font-medium text-label-neutral transition-colors hover:text-label-normal">초기화</button>
                        <button type="button" @click="editKey = null; $nextTick(() => $el.closest('form')?.submit())" class="rounded-lg bg-primary px-3.5 py-1.5 text-label-1 font-medium text-white transition-colors hover:bg-primary-strong">적용</button>
                    </div>
                </div>
            </div>
        </template>

        {{-- 필터 추가: 테이블 헤더 컬럼 목록 드롭다운 --}}
        @if ($addable)
        <div class="relative" @click.outside="addOpen = false">
            <button type="button" @click="addOpen = ! addOpen"
                    class="inline-flex h-7 items-center gap-1 rounded-lg border border-line-solid-normal px-2.5 text-label-2 font-medium text-label-alternative transition-colors hover:bg-fill-alternative"
                    :class="addOpen && 'bg-fill-alternative'">
                <x-icon-plus class="h-3 w-3" />필터 추가
            </button>
            <div x-show="addOpen" x-cloak
                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute left-0 top-full z-30 mt-1.5 w-[180px] overflow-hidden rounded-lg border border-line-solid-neutral bg-background-normal py-1 shadow-elevation-lg">
                <p class="px-3 pb-1 pt-2 text-caption-1 font-medium text-label-assistive">필터 항목</p>
                <template x-for="c in columns" :key="c.key">
                    <button type="button" @click="toggleColumn(c.key)"
                            class="flex w-full items-center gap-2.5 px-3 py-2.5 text-left text-label-1 transition-colors"
                            :class="isActive(c.key) ? 'font-medium text-primary' : 'text-label-normal hover:bg-fill-alternative'">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border-[1.5px] transition-colors"
                              :class="isActive(c.key) ? 'border-primary bg-primary text-white' : 'border-line-normal-strong'">
                            <x-icon-check class="h-[90%] w-[90%] [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" x-show="isActive(c.key)" />
                        </span>
                        <span x-text="c.label"></span>
                    </button>
                </template>
            </div>
        </div>
        @endif

        {{-- 전체 초기화 --}}
        @if ($resettable)
        <button type="button" @click="resetAll()"
                class="px-1.5 py-1 text-caption-1 font-medium text-label-neutral transition-colors hover:text-label-normal">초기화</button>
        @endif
    </div>

    {{-- 활성 필터 값 → hidden input (GET 폼에 포함되어 서버로 전달) --}}
    <template x-for="k in active" :key="'hid-'+k">
        @php $arrTypes = "['select','search','region'].includes(col(k)?.type)"; @endphp
        <input type="hidden"
               :name="{{ $arrTypes }} && values[k] && values[k].length ? k : ''"
               :value="{{ $arrTypes }} && values[k] ? values[k].join(',') : ''">
    </template>

    @if (! is_null($count))
        <span class="shrink-0 text-label-1 text-label-alternative">총 {{ $count }}{{ $countUnit }}</span>
    @endif
</div>
