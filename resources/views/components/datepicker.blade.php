{{-- DS 데이트피커 — 네이티브 type=date 대신 커스텀 캘린더(Alpine).
     헤더: 연/월 드롭다운 + 이동 화살표(‹ › 월, « » 연). 선택값은 hidden input으로 제출.
     props:
       label / name / placeholder / value('YYYY-MM-DD') / hint / error / required
       minYear / maxYear : 연 드롭다운 범위 (기본: 올해-100 ~ 올해+10) --}}
@props([
    'label' => null,
    'name' => null,
    'placeholder' => '날짜 선택',
    'value' => null,
    'hint' => null,
    'error' => null,
    'required' => false,
    'minYear' => null,
    'maxYear' => null,
    'size' => 'lg',            // sm(조밀·필터 등) | md | lg(폼 기본)
])

@php
    $id = $attributes->get('id') ?? $name ?? 'date-' . uniqid();
    $hasError = filled($error);
    $border = $hasError
        ? 'border-status-negative focus-visible:ring-status-negative/30'
        : 'border-line-solid-normal focus-visible:border-primary focus-visible:ring-primary/30';

    // 트리거 사이즈 (캘린더 팝오버는 동일)
    $sizes = [
        'sm' => ['box' => 'h-9 gap-1.5 rounded-lg pl-3 pr-2.5', 'text' => 'text-label-1', 'icon' => 'h-4 w-4'],
        'md' => ['box' => 'h-10 gap-2 rounded-lg pl-3.5 pr-3', 'text' => 'text-body-2', 'icon' => 'h-[18px] w-[18px]'],
        'lg' => ['box' => 'h-12 gap-2 rounded-xl pl-4 pr-3', 'text' => 'text-body-1', 'icon' => 'h-5 w-5'],
    ];
    $sz = $sizes[$size] ?? $sizes['lg'];

    // Alpine 하이드레이션 전에도 비지 않도록 서버에서 표시 텍스트 미리 렌더
    $serverDisplay = $placeholder;
    if (filled($value) && preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', (string) $value, $m)) {
        $serverDisplay = (int) $m[1] . '년 ' . (int) $m[2] . '월 ' . (int) $m[3] . '일';
    }
    $serverDisplayColor = filled($value) ? 'text-label-normal' : 'text-label-assistive';
@endphp

<div class="flex flex-col gap-1.5"
     x-data="dsDatepicker({ initial: @js((string) $value), placeholder: @js($placeholder), minYear: @js($minYear), maxYear: @js($maxYear) })"
     x-modelable="value"
     {{ $attributes->whereStartsWith('x-model') }}
     @keydown.escape.stop="open = false">
    @if ($label)
        <label for="{{ $id }}" class="text-label-1 font-medium text-label-neutral">
            {{ $label }}@if ($required)<span class="text-status-negative">*</span>@endif
        </label>
    @endif

    <div class="relative" @click.outside="open = false; panel = null">
        <button type="button" id="{{ $id }}" x-ref="trigger" @click="toggle()" :aria-expanded="open" aria-haspopup="dialog"
                class="flex w-full items-center {{ $sz['box'] }} border bg-background-normal text-left shadow-elevation-xs transition-colors duration-150 focus:outline-none focus-visible:ring-2 {{ $border }}">
            <x-icon-calendar class="{{ $sz['icon'] }} shrink-0 text-label-assistive" />
            <span class="flex-1 truncate {{ $sz['text'] }} {{ $serverDisplayColor }}" :class="value ? 'text-label-normal' : 'text-label-assistive'" x-text="display">{{ $serverDisplay }}</span>
            <x-icon-caret-down class="{{ $sz['icon'] }} shrink-0 text-label-alternative transition-transform duration-200" ::class="open && 'rotate-180'" />
        </button>

        <input type="hidden" @if ($name) name="{{ $name }}" @endif :value="value">

        {{-- 캘린더 팝오버 — position:fixed 로 트리거에 앵커링 → 스크롤 모달/overflow 안에서도 안 잘림 --}}
        <div x-show="open" x-cloak :style="anchorStyle()"
             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             role="dialog" aria-label="날짜 선택"
             class="rounded-2xl border border-line-solid-neutral bg-background-normal p-4 shadow-elevation-lg">
            {{-- 헤더: [연▾ 월▾]  ‹ 오늘 › (청담원 결 — 굵은 월 타이틀 + 깔끔한 이동) --}}
            <div class="flex items-center justify-between gap-2">
                <div class="relative flex items-center gap-0.5">
                    <button type="button" @click="togglePanel('year')"
                            class="rounded-lg px-1.5 py-1 text-body-1 font-bold text-label-strong transition-colors hover:bg-fill-alternative">
                        <span x-text="`${year}년`"></span>
                    </button>
                    <button type="button" @click="togglePanel('month')"
                            class="flex items-center gap-1 rounded-lg px-1.5 py-1 text-body-1 font-bold text-label-strong transition-colors hover:bg-fill-alternative">
                        <span x-text="`${month + 1}월`"></span>
                        <x-icon-caret-down class="h-4 w-4 text-label-alternative transition-transform duration-200" ::class="panel === 'month' && 'rotate-180'" />
                    </button>

                    {{-- 연 패널 — 바깥 박스(흰색 채움·테두리)와 스크롤 분리 (overscroll에도 흰색 유지) --}}
                    <div x-show="panel === 'year'" x-cloak
                         class="absolute left-0 top-full z-10 mt-1.5 w-28 overflow-hidden rounded-xl border border-line-solid-neutral bg-background-normal shadow-elevation-lg">
                        <div x-ref="yearList" class="max-h-56 overflow-y-auto overscroll-contain py-1">
                            <template x-for="y in years" :key="y">
                                <button type="button" @click="setYear(y)" :data-sel="y === year ? '1' : '0'"
                                        class="block w-full px-3 py-1.5 text-left text-body-2 transition-colors"
                                        :class="y === year ? 'bg-primary-surface font-semibold text-primary' : 'text-label-normal hover:bg-fill-alternative'"
                                        x-text="`${y}년`"></button>
                            </template>
                        </div>
                    </div>

                    {{-- 월 패널 --}}
                    <div x-show="panel === 'month'" x-cloak
                         class="absolute left-0 top-full z-10 mt-1.5 w-40 rounded-xl border border-line-solid-neutral bg-background-normal p-2 shadow-elevation-lg">
                        <div class="grid grid-cols-3 gap-1">
                            <template x-for="m in 12" :key="m">
                                <button type="button" @click="setMonth(m - 1)"
                                        class="rounded-lg py-1.5 text-body-2 transition-colors"
                                        :class="(m - 1) === month ? 'bg-primary font-semibold text-white' : 'text-label-normal hover:bg-fill-alternative'"
                                        x-text="`${m}월`"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-0.5">
                    <button type="button" @click="prevMonth()" aria-label="이전 달" class="rounded-lg p-1.5 text-label-alternative transition-colors hover:bg-fill-alternative hover:text-label-normal"><x-icon-chevron-left class="h-4 w-4" /></button>
                    <button type="button" @click="goToday()" class="rounded-lg px-2 py-1 text-label-2 font-semibold text-label-neutral transition-colors hover:bg-fill-alternative">오늘</button>
                    <button type="button" @click="nextMonth()" aria-label="다음 달" class="rounded-lg p-1.5 text-label-alternative transition-colors hover:bg-fill-alternative hover:text-label-normal"><x-icon-chevron-right class="h-4 w-4" /></button>
                </div>
            </div>

            {{-- 요일 --}}
            <div class="mt-2.5 grid grid-cols-7">
                <template x-for="(w, i) in weekdays" :key="w">
                    <span class="flex h-9 items-center justify-center text-caption-1 font-semibold"
                          :class="i === 0 ? 'text-status-negative/80' : (i === 6 ? 'text-accent-fg-blue/80' : 'text-label-assistive')"
                          x-text="w"></span>
                </template>
            </div>

            {{-- 날짜 — 원형 셀(선택=채움 / 오늘=링 / 주말=색) --}}
            <div class="grid grid-cols-7 gap-y-1">
                <template x-for="(c, i) in grid" :key="i">
                    <div class="flex justify-center">
                        <template x-if="c">
                            <button type="button" @click="select(c)"
                                    class="flex h-10 w-10 items-center justify-center rounded-full text-body-2 transition-colors"
                                    :class="dayClass(c, i)"
                                    x-text="c"></button>
                        </template>
                        <template x-if="!c"><span class="h-10 w-10"></span></template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @if ($hasError)
        <p class="text-label-2 text-status-negative">{{ $error }}</p>
    @elseif ($hint)
        <p class="text-label-2 text-label-assistive">{{ $hint }}</p>
    @endif
</div>

@once
@push('scripts')
<script>
    function dsDatepicker({ initial, placeholder, minYear, maxYear }) {
        const parse = (s) => {
            if (!s) return null;
            const p = s.split('-').map(Number);
            if (p.length < 3 || p.some(isNaN)) return null;
            return { y: p[0], m: p[1] - 1, d: p[2] };
        };
        const init = parse(initial);
        const thisYear = new Date().getFullYear();
        const base = init
            ? new Date(init.y, init.m, 1)
            : (() => { const t = new Date(); return new Date(t.getFullYear(), t.getMonth(), 1); })();
        return {
            open: false,
            panel: null,            // null | 'year' | 'month'
            value: initial || '',
            view: base,
            placeholder,
            minYear: minYear || (thisYear - 100),
            maxYear: maxYear || (thisYear + 10),
            weekdays: ['일', '월', '화', '수', '목', '금', '토'],
            anchor: { top: 0, left: 0, width: 340, up: false },
            _onScroll: null,
            init() {
                this.$watch('open', (v) => {
                    if (v) {
                        // x-model 등 외부에서 들어온 value의 달로 뷰 이동 (프리필 반영)
                        if (this.value) { const [yy, mm] = this.value.split('-').map(Number); this.view = new Date(yy, mm - 1, 1); }
                        this.$nextTick(() => this.position());
                        this._onScroll = () => { if (this.open) this.position() };
                        window.addEventListener('scroll', this._onScroll, true);
                        window.addEventListener('resize', this._onScroll);
                    } else if (this._onScroll) {
                        window.removeEventListener('scroll', this._onScroll, true);
                        window.removeEventListener('resize', this._onScroll);
                        this._onScroll = null;
                    }
                });
            },
            position() {
                const el = this.$refs.trigger;
                if (!el) return;
                const t = el.getBoundingClientRect();
                const gap = 6, h = 380;
                const below = window.innerHeight - t.bottom - gap;
                const above = t.top - gap;
                const up = below < h && above > below;
                const w = Math.min(340, window.innerWidth - 16);
                let left = Math.round(t.left);
                if (left + w > window.innerWidth - 8) left = Math.round(window.innerWidth - 8 - w);
                if (left < 8) left = 8;
                this.anchor = { up, left, width: w, top: Math.round(up ? t.top - gap : t.bottom + gap) };
            },
            anchorStyle() {
                const b = `position:fixed;left:${this.anchor.left}px;width:${this.anchor.width}px;z-index:60;`;
                return this.anchor.up
                    ? b + `bottom:${Math.round(window.innerHeight - this.anchor.top)}px;`
                    : b + `top:${this.anchor.top}px;`;
            },
            get year() { return this.view.getFullYear() },
            get month() { return this.view.getMonth() },
            get years() {
                const arr = [];
                for (let y = this.maxYear; y >= this.minYear; y--) arr.push(y);
                return arr;
            },
            get display() {
                if (!this.value) return this.placeholder;
                const [y, m, d] = this.value.split('-').map(Number);
                return `${y}년 ${m}월 ${d}일`;
            },
            get grid() {
                const startDay = new Date(this.year, this.month, 1).getDay();
                const days = new Date(this.year, this.month + 1, 0).getDate();
                const cells = [];
                for (let i = 0; i < startDay; i++) cells.push(null);
                for (let d = 1; d <= days; d++) cells.push(d);
                while (cells.length % 7 !== 0) cells.push(null);
                return cells;
            },
            isSelected(d) {
                if (!d || !this.value) return false;
                const [y, m, da] = this.value.split('-').map(Number);
                return y === this.year && (m - 1) === this.month && da === d;
            },
            isToday(d) {
                if (!d) return false;
                const t = new Date();
                return t.getFullYear() === this.year && t.getMonth() === this.month && t.getDate() === d;
            },
            // 셀 스타일: 선택=채움 원 / 오늘=primary 링 / 주말=색 / 평일=기본
            dayClass(c, i) {
                if (this.isSelected(c)) return 'bg-primary font-bold text-white shadow-elevation-xs';
                if (this.isToday(c)) return 'font-bold text-primary ring-1 ring-inset ring-primary/40 hover:bg-primary-surface';
                const col = i % 7;
                const tone = col === 0 ? 'text-status-negative' : (col === 6 ? 'text-accent-fg-blue' : 'text-label-normal');
                return `${tone} hover:bg-fill-alternative`;
            },
            goToday() { const t = new Date(); this.view = new Date(t.getFullYear(), t.getMonth(), 1); this.panel = null; },
            toggle() { this.open = !this.open; if (!this.open) this.panel = null; },
            togglePanel(p) {
                this.panel = this.panel === p ? null : p;
                if (this.panel === 'year') {
                    this.$nextTick(() => {
                        const el = this.$refs.yearList && this.$refs.yearList.querySelector('[data-sel="1"]');
                        if (el) el.scrollIntoView({ block: 'center' });
                    });
                }
            },
            prevMonth() { this.view = new Date(this.year, this.month - 1, 1); this.panel = null; },
            nextMonth() { this.view = new Date(this.year, this.month + 1, 1); this.panel = null; },
            prevYear() { this.view = new Date(this.year - 1, this.month, 1); this.panel = null; },
            nextYear() { this.view = new Date(this.year + 1, this.month, 1); this.panel = null; },
            setYear(y) { this.view = new Date(y, this.month, 1); this.panel = null; },
            setMonth(m) { this.view = new Date(this.year, m, 1); this.panel = null; },
            select(d) {
                if (!d) return;
                const mm = String(this.month + 1).padStart(2, '0');
                const dd = String(d).padStart(2, '0');
                this.value = `${this.year}-${mm}-${dd}`;
                this.open = false;
                this.panel = null;
            },
        };
    }
</script>
@endpush
@endonce
