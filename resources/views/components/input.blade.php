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
    'size' => 'lg',                 // sm(h-9) | md(h-10·조밀) | lg(h-12·폼 기본)
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

    // 상태별 보더/포커스 (negative > positive > normal)
    $normalBorder = 'border-line-solid-normal focus:border-primary focus:ring-2 focus:ring-primary/30';
    $errorBorder = 'border-status-negative focus:border-status-negative focus:ring-2 focus:ring-status-negative/30';
    $successBorder = 'border-status-positive focus:border-status-positive focus:ring-2 focus:ring-status-positive/30';
    // 에러/실시간검증은 :class 반응형으로 보더 부여 → 정적 클래스엔 미포함
    $stateBorder = ($hasError || $hasLive) ? '' : ($hasSuccess ? $successBorder : $normalBorder);

    $sizeCls = [
        'sm' => 'h-9 rounded-lg text-label-1',
        'md' => 'h-10 rounded-lg text-body-2',
        'lg' => 'h-12 rounded-xl text-body-1',
    ][$size] ?? 'h-12 rounded-xl text-body-1';

    // 폼 기본(lg)은 라벨을 body-1(16px)로 — 시니어 가독성. 조밀한 sm/md(테이블·필터)는 14px 유지.
    $labelCls = $size === 'lg' ? 'text-body-1' : 'text-label-1';

    $fieldClasses = implode(' ', [
        'peer w-full bg-background-normal text-label-normal placeholder:text-label-assistive shadow-elevation-xs',
        $sizeCls,
        'border transition-colors duration-150 focus:outline-none',
        $icon ? 'pl-11' : 'pl-4',
        $clearable || $revealable ? 'pr-11' : ($hasCounter ? 'pr-16' : 'pr-4'),
        $stateBorder,
        'disabled:bg-interaction-disable disabled:text-label-disable disabled:cursor-not-allowed',
    ]);
@endphp

<div class="flex flex-col gap-1.5" @if ($hasError) x-data="{ err: true }" @endif @if ($hasLive) :data-error="({{ $liveState }}) === 'error'" @endif>
    @if ($label)
        <label for="{{ $id }}" class="{{ $labelCls }} font-medium text-label-neutral">
            {{ $label }}
            @if ($required)<span class="text-status-negative">*</span>@endif
        </label>
    @endif

    <div class="relative"
         @if ($needsState) x-data="{ v: @js($initialValue)@if ($revealable), show: false @endif }" x-modelable="v" {{ $attributes->whereStartsWith('x-model') }} @endif>
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-label-assistive">
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
