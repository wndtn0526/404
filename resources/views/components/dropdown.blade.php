@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'placeholder' => '선택하세요',
    'options' => [],            // [value => label] 연관배열
    'selected' => null,
    'required' => false,
    'size' => 'lg',             // 'md' (테이블 행 등 조밀한 곳) | 'lg' (폼 기본)
    'liveState' => null,        // Alpine 식: 'ok' | 'error' | 그외(중립) — 클라이언트 실시간 검증 상태 (input과 동일 API)
    'liveMsg' => null,          // Alpine 식: 표시할 메시지(값 있으면 노출). liveState와 함께 사용
])

@php
    $id = $attributes->get('id') ?? $name ?? 'dropdown-' . uniqid();
    $hasError = filled($error);
    $hasLive = filled($liveState);  // 클라이언트 실시간 검증 모드 (반응형 테두리+메시지)
    $selectedStr = $selected === null ? '' : (string) $selected;

    // Alpine 용 [{value,label}] 정규화
    $opts = [];
    foreach ($options as $val => $lbl) {
        $opts[] = ['value' => (string) $val, 'label' => $lbl];
    }

    // Alpine 하이드레이션 전에도 비지 않도록 서버에서 표시 텍스트 미리 렌더
    $serverDisplay = $options[$selectedStr] ?? $placeholder;
    $serverDisplayColor = $selectedStr !== '' ? 'text-label-normal' : 'text-label-assistive';

    $sizes = [
        'sm' => ['trigger' => 'h-8 gap-1.5 rounded-md px-2.5', 'text' => 'text-label-2', 'icon' => 'w-4 h-4', 'option' => 'px-3 py-1.5'],
        'md' => ['trigger' => 'h-10 gap-2 rounded-md px-3', 'text' => 'text-body-2', 'icon' => 'w-4 h-4', 'option' => 'px-4 py-2'],
        'lg' => ['trigger' => 'h-10 gap-3 rounded-md px-4', 'text' => 'text-body-1', 'icon' => 'w-5 h-5', 'option' => 'px-5 py-3'],
    ];
    $sz = $sizes[$size] ?? $sizes['lg'];

    // 폼 기본(lg)은 라벨을 body-1(16px)로 — 시니어 가독성. 조밀한 sm/md(테이블·필터)는 14px 유지.
    $labelCls = $size === 'lg' ? 'text-body-1' : 'text-label-1';

    // 테두리 색은 반응형 검증(liveState) 시 :class로 부여하므로 정적 클래스엔 미포함
    $errBorderCls = 'border-status-negative focus-visible:border-status-negative focus-visible:ring-status-negative/30';
    $okBorderCls = 'border-line-normal-neutral focus-visible:border-primary focus-visible:ring-primary/30';
    $trigger = implode(' ', [
        'flex w-full items-center justify-between text-left border bg-background-normal',
        $sz['trigger'],
        'transition-colors duration-150 focus:outline-none focus-visible:ring-2',
        $hasLive ? '' : ($hasError ? $errBorderCls : $okBorderCls),
    ]);
@endphp

<div class="flex flex-col gap-1.5"
     @if ($hasLive) :data-error="({{ $liveState }}) === 'error'" @endif
     x-data="{
        open: false,
        highlighted: -1,
        value: @js($selectedStr),
        options: @js($opts),
        placeholder: @js($placeholder),
        anchor: { top: 0, left: 0, width: 0, maxH: 256, up: false },
        _onScroll: null,
        get selectedOption() { return this.options.find(o => String(o.value) === String(this.value)) || null },
        get display() { return this.selectedOption ? this.selectedOption.label : this.placeholder },
        get hasValue() { return this.value !== '' && this.value !== null },
        position() {
            const t = this.$refs.trigger.getBoundingClientRect();
            const gap = 6;
            const below = window.innerHeight - t.bottom - gap;
            const above = t.top - gap;
            const want = Math.min(256, this.options.length * 44 + 8);
            const up = below < want && above > below;
            this.anchor.up = up;
            this.anchor.left = Math.round(t.left);
            this.anchor.width = Math.round(t.width);
            this.anchor.top = Math.round(up ? t.top - gap : t.bottom + gap);
            this.anchor.maxH = Math.max(140, Math.min(256, Math.round(up ? above : below)));
        },
        anchorStyle() {
            const b = `position:fixed;left:${this.anchor.left}px;width:${this.anchor.width}px;z-index:60;`;
            return this.anchor.up
                ? b + `bottom:${Math.round(window.innerHeight - this.anchor.top)}px;`
                : b + `top:${this.anchor.top}px;`;
        },
        openMenu() {
            this.open = true;
            const i = this.options.findIndex(o => String(o.value) === String(this.value));
            this.highlighted = i < 0 ? 0 : i;
            this.$nextTick(() => this.position());
            this._onScroll = () => { if (this.open) this.position() };
            window.addEventListener('scroll', this._onScroll, true);
            window.addEventListener('resize', this._onScroll);
        },
        close() {
            this.open = false;
            if (this._onScroll) {
                window.removeEventListener('scroll', this._onScroll, true);
                window.removeEventListener('resize', this._onScroll);
                this._onScroll = null;
            }
        },
        toggle() { this.open ? this.close() : this.openMenu() },
        move(d) {
            if (!this.open) { this.openMenu(); return }
            const n = this.options.length;
            if (n) this.highlighted = (this.highlighted + d + n) % n;
        },
        choose(v) { this.value = String(v); this.close(); this.$refs.trigger.focus(); @if ($hasLive) this.$dispatch('field-touched', { name: '{{ $name }}' }); @endif },
        chooseHighlighted() {
            if (this.open && this.options[this.highlighted]) this.choose(this.options[this.highlighted].value);
            else this.openMenu();
        },
     }"
     x-modelable="value"
     {{ $attributes->whereStartsWith('x-model') }}
     @keydown.escape.stop="open && (open = false)">

    @if ($label)
        <label for="{{ $id }}" class="{{ $labelCls }} font-medium text-label-neutral">
            {{ $label }}
            @if ($required)<span class="text-status-negative">*</span>@endif
        </label>
    @endif

    <div class="relative" @click.outside="close()">
        <button
            type="button"
            id="{{ $id }}"
            x-ref="trigger"
            @click="toggle()"
            @keydown.arrow-down.prevent="move(1)"
            @keydown.arrow-up.prevent="move(-1)"
            @keydown.enter.prevent="chooseHighlighted()"
            @keydown.space.prevent="chooseHighlighted()"
            @keydown.home.prevent="open && (highlighted = 0)"
            @keydown.end.prevent="open && (highlighted = options.length - 1)"
            :aria-expanded="open"
            aria-haspopup="listbox"
            @if ($hasLive) :class="({{ $liveState }}) === 'error' ? '{{ $errBorderCls }}' : '{{ $okBorderCls }}'" @endif
            {{ $attributes->whereDoesntStartWith('x-model')->class($trigger) }}
        >
            <span class="truncate {{ $sz['text'] }} {{ $serverDisplayColor }}" :class="hasValue ? 'text-label-normal' : 'text-label-assistive'" x-text="display">{{ $serverDisplay }}</span>
            <x-icon-caret-down class="{{ $sz['icon'] }} shrink-0 text-label-alternative transition-transform duration-200" ::class="open && 'rotate-180'" />
        </button>

        <input type="hidden" @if ($name) name="{{ $name }}" @endif :value="value">

        {{-- 메뉴: position:fixed 로 트리거에 앵커링 → 스크롤 모달/overflow 안에서도 안 잘림.
             바깥 박스는 흰색 채움·테두리·둥근모서리(overscroll 시 흰색 유지), 안쪽 ul만 스크롤. --}}
        <div
            x-show="open"
            x-cloak
            :style="anchorStyle()"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="overflow-hidden rounded-md border border-line-solid-neutral bg-background-normal shadow-elevation-lg"
        >
            <ul role="listbox" :style="`max-height:${anchor.maxH}px`" class="overflow-y-auto overscroll-contain py-1 focus:outline-none">
                <template x-for="(o, i) in options" :key="o.value">
                    <li
                        role="option"
                        :aria-selected="String(o.value) === String(value)"
                        @click="choose(o.value)"
                        @mouseenter="highlighted = i"
                        class="flex cursor-pointer select-none items-center gap-2 {{ $sz['option'] }}"
                        :class="highlighted === i ? 'bg-fill-alternative' : ''"
                    >
                        <span
                            class="flex-1 truncate {{ $sz['text'] }}"
                            :class="String(o.value) === String(value) ? 'font-medium text-primary' : 'text-label-normal'"
                            x-text="o.label"
                        ></span>
                        <x-icon-check class="{{ $sz['icon'] }} shrink-0 text-primary" x-show="String(o.value) === String(value)" />
                    </li>
                </template>
            </ul>
        </div>
    </div>

    @if ($hasLive)
        {{-- 반응형 검증 메시지 (error=빨강 · 그외=중립) — 폼 하한 14px(label-1) --}}
        <p x-show="{{ $liveMsg }}" x-cloak x-text="{{ $liveMsg }}" class="text-label-1"
           :class="({{ $liveState }}) === 'error' ? 'text-status-negative' : 'text-label-assistive'"></p>
        @if ($hint)
            <p x-show="! ({{ $liveMsg }})" x-cloak class="text-label-1 text-label-assistive">{{ $hint }}</p>
        @endif
    @elseif ($hasError)
        <p class="text-label-2 text-status-negative">{{ $error }}</p>
    @elseif ($hint)
        <p class="text-label-2 text-label-assistive">{{ $hint }}</p>
    @endif
</div>
