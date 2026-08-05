@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'hint' => null,                 // 하단 보조 설명
    'error' => null,                // 에러(negative) 메시지 — 있으면 에러 스타일
    'success' => null,              // 성공(positive) 메시지 — 있으면 성공 스타일
    'icon' => null,                 // 앞쪽(leading) 아이콘
    'required' => false,
    'clearable' => false,           // 입력 시 DS circle-close 클리어 버튼 표시
    'revealable' => false,          // password 표시/숨김(eye) 토글 — type=password 전용
    'maxlength' => null,            // 지정 시 우측에 글자 수 카운터 + 네이티브 입력 제한
    'minlength' => null,
    'size' => 'lg',                 // sm(h-8 · 원본 Box 32) | md·lg(h-10 · 원본 Box 40)
    'variant' => 'box',             // box(원본 Input Box 40/32) | line(원본 Line Input)
    'tags' => [],                   // 원본 Input Box Tag — 박스 안에 칩으로 얹는 값들
    'liveState' => null,            // Alpine 식: 'ok' | 'error' | 그외(중립) — 비동기 실시간 검증 상태
    'liveMsg' => null,              // Alpine 식: 표시할 메시지(값 있으면 노출). liveState와 함께 사용
])

@php
    $id = $attributes->get('id') ?? $name ?? 'input-' . uniqid();
    $hasError = filled($error);
    $hasLive = filled($liveState);  // 클라이언트 실시간 검증 모드
    $hasSuccess = !$hasError && filled($success);
    $hasCounter = filled($maxlength);
    // clearable·counter·revealable·x-model 중 하나라도 있으면 로컬 v(x-data) 필요
    $needsState = $clearable || $hasCounter || $revealable;
    $initialValue = (string) ($attributes->get('value') ?? ($name !== null ? old($name, '') : ''));

    // ── 원본 Input Status 실측 (Figma 1002:518593 · Version × Status 매트릭스) ──
    //   Default   border  warm-gray-200
    //   Hover     bg      warm-gray-50
    //   Active    border  deep-blue-900
    //   Success   보더는 Default 그대로 — 초록(status-positive)은 하단 메시지에만 쓴다
    //   Error     border  status-negative
    //   Disabled  border·bg warm-gray-100 · text warm-gray-400
    // ⚠️ Active 는 브랜드색(검정)이 아니다. 원본이 deep blue 라 그대로 따른다.
    // ⚠️ Active 는 보더 색만 바뀐다 — 링(ring)도 그림자도 없다. 원본에 그 클래스가 없다.
    //    캐럿(cursor bar)도 같은 deep blue 다. (Figma 1002:518831 · 1002:518772)
    $activeBorder = 'focus:border-deep-blue-900';
    $normalBorder = "border-line-solid-normal hover:bg-background-elevated-alternative {$activeBorder}";
    $errorBorder = 'border-status-negative focus:border-status-negative';
    $successBorder = "border-line-solid-normal {$activeBorder}";
    // 에러/실시간검증은 :class 반응형으로 보더 부여 → 정적 클래스엔 미포함
    $stateBorder = ($hasError || $hasLive) ? '' : ($hasSuccess ? $successBorder : $normalBorder);

    // 원본 실측: Box 40 / Box 32 두 높이만 있고 본문은 14px 고정. 높이는 늘어나지 않는다.
    $sizeCls = [
        'sm' => 'h-8 text-label-1',
        'md' => 'h-10 text-label-1',
        'lg' => 'h-10 text-label-1',
    ][$size] ?? 'h-10 text-label-1';

    // 원본 Line Input — 밑줄만 있고 좌우 패딩이 없다
    $isLine = $variant === 'line';
    $shapeCls = $isLine ? 'border-0 border-b rounded-none' : 'border rounded-md';
    $hasTags = filled($tags);

    // 폼 기본(lg)은 라벨을 body-1(16px)로 — 시니어 가독성. 조밀한 sm/md(테이블·필터)는 14px 유지.
    $labelCls = $size === 'lg' ? 'text-body-1' : 'text-label-1';

    $fieldClasses = implode(' ', [
        // 캐럿은 원본 cursor bar 와 같은 deep blue
        'peer w-full bg-background-normal text-label-strong caret-deep-blue-900 placeholder:text-label-assistive',
        $sizeCls,
        $shapeCls,
        'transition-colors duration-150 focus:outline-none',
        // 원본 실측: 박스 안쪽 좌우 패딩 11px 고정
        $isLine ? ($icon ? 'pl-7' : 'pl-0') : ($icon ? 'pl-10' : 'pl-[11px]'),
        $isLine
            ? ($clearable || $revealable ? 'pr-7' : ($hasCounter ? 'pr-12' : 'pr-0'))
            : ($clearable || $revealable ? 'pr-10' : ($hasCounter ? 'pr-14' : 'pr-[11px]')),
        $stateBorder,
        // 원본 Disabled — 보더·배경 모두 Warm gray/100, 글자 Warm gray/400
        'disabled:border-interaction-disable disabled:bg-interaction-disable disabled:text-label-disable disabled:cursor-not-allowed disabled:hover:bg-interaction-disable',
    ]);

    // 원본 Input Box Tag — 칩을 담는 박스. 안쪽 좌우 패딩 고정, 양옆으로만 늘어난다.
    $tagBoxClasses = implode(' ', [
        'flex w-full flex-wrap items-center gap-1.5 bg-background-normal px-[11px]',
        $size === 'sm' ? 'min-h-8 py-1' : 'min-h-10 py-1.5',
        $shapeCls,
        // Tag/Tags 는 원본에서 Default·Hover·Disabled 세 상태만 정의돼 있다
        'transition-colors duration-150 focus-within:border-deep-blue-900',
        $hasError ? 'border-status-negative' : 'border-line-solid-normal hover:bg-background-elevated-alternative',
    ]);
@endphp

<div class="flex flex-col gap-1.5" @if ($hasError) x-data="{ err: true }" @endif @if ($hasLive) :data-error="({{ $liveState }}) === 'error'" @endif>
    @if ($label)
        <label for="{{ $id }}" class="{{ $labelCls }} font-medium text-label-neutral">
            {{ $label }}
            @if ($required)<span class="text-status-negative">*</span>@endif
        </label>
    @endif

    @if ($hasTags)
        {{-- 원본 Input Box Tag — 칩 + 이어서 입력 --}}
        <div class="{{ $tagBoxClasses }}">
            @foreach ((array) $tags as $tag)
                <x-chip size="small" variant="solid">{{ $tag }}</x-chip>
            @endforeach

            <input
                id="{{ $id }}"
                type="{{ $type }}"
                @if ($name) name="{{ $name }}" @endif
                @if ($required) required @endif
                {{ $attributes->class('min-w-24 flex-1 border-0 bg-transparent p-0 text-label-1 text-label-strong caret-deep-blue-900 placeholder:text-label-assistive focus:outline-none focus:ring-0') }}
            />
        </div>
    @else
    <div class="relative"
         @if ($needsState) x-data="{ v: @js($initialValue)@if ($revealable), show: false @endif }" x-modelable="v" {{ $attributes->whereStartsWith('x-model') }} @endif>
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center {{ $isLine ? 'pl-0' : 'pl-[11px]' }} text-label-assistive">
                <x-dynamic-component :component="'icon-' . $icon" class="w-5 h-5" />
            </span>
        @endif

        <input
            id="{{ $id }}"
            type="{{ $type }}"
            @if ($revealable) :type="show ? 'text' : @js($type)" @endif
            @if ($name) name="{{ $name }}" @endif
            @if ($required) required @endif
            @if ($hasCounter) maxlength="{{ $maxlength }}" @if ($minlength) minlength="{{ $minlength }}" @endif @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-msg" @input="err = false" @change="err = false" :class="err ? '{{ $errorBorder }}' : '{{ $normalBorder }}'" @endif
            @if ($hasLive) :class="({{ $liveState }}) === 'ok' ? '{{ $successBorder }}' : (({{ $liveState }}) === 'error' ? '{{ $errorBorder }}' : '{{ $normalBorder }}')" @input="$dispatch('field-touched', { name: '{{ $name }}' })" @endif
            @if ($needsState) x-ref="cf" x-model="v" @endif
            @if ($needsState) {{ $attributes->whereDoesntStartWith('x-model')->class($fieldClasses) }} @else {{ $attributes->class($fieldClasses) }} @endif
        />

        @if ($revealable)
            {{-- DS 표시/숨김 토글 (eye / eye-slash) --}}
            <button type="button" @click="show = !show"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-label-assistive transition-colors hover:text-label-alternative peer-disabled:hidden"
                    :aria-label="show ? '비밀번호 숨기기' : '비밀번호 표시'">
                <x-icon-eye x-show="!show" class="h-[22px] w-[22px]" />
                <x-icon-eye-slash x-show="show" x-cloak class="h-[22px] w-[22px]" />
            </button>
        @elseif ($clearable)
            {{-- DS 클리어 버튼 (circle-close) — 입력값 있을 때만 --}}
            <button type="button" x-show="v.length" x-cloak
                    @click="v = ''; $refs.cf.focus()"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-label-assistive transition-colors hover:text-label-alternative peer-disabled:hidden"
                    aria-label="입력 지우기">
                <x-icon-circle-close class="h-[22px] w-[22px]" />
            </button>
        @elseif ($hasCounter)
            {{-- 글자 수 카운터 --}}
            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-label-2 font-medium text-label-alternative">
                <span x-text="v.length"></span>/{{ $maxlength }}
            </span>
        @endif
    </div>
    @endif

    @if ($hasLive)
        {{-- 실시간 검증 메시지 (ok=초록 · error=빨강 · 그외=중립) --}}
        <p x-show="{{ $liveMsg }}" x-cloak x-text="{{ $liveMsg }}" class="text-label-1"
           :class="({{ $liveState }}) === 'ok' ? 'text-status-positive' : (({{ $liveState }}) === 'error' ? 'text-status-negative' : 'text-label-assistive')"></p>
        @if ($hint)
            <p x-show="! ({{ $liveMsg }})" x-cloak class="text-label-1 text-label-assistive">{{ $hint }}</p>
        @endif
    @elseif ($hasError)
        <p id="{{ $id }}-msg" x-show="err" class="text-label-2 text-status-negative">{{ $error }}</p>
        @if ($hint)
            <p x-show="! err" x-cloak class="text-label-2 text-label-assistive">{{ $hint }}</p>
        @endif
    @elseif ($hasSuccess)
        <p class="text-label-2 text-status-positive">{{ $success }}</p>
    @elseif ($hint)
        <p class="text-label-2 text-label-assistive">{{ $hint }}</p>
    @endif
</div>
