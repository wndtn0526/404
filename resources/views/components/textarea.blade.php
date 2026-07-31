{{-- DS Textarea — Figma 16215:32165
     maxlength 지정 시 박스 아래 footer 행(좌: hint/error · 우: '현재/최대' 카운터)에 글자 수 표시.
     x-model 패스스루(x-modelable)로 부모 상태 바인딩 가능. --}}
@props([
    'label' => null,
    'name' => null,
    'hint' => null,
    'error' => null,
    'rows' => 4,
    'required' => false,
    'maxlength' => null,   // 지정 시 글자 수 카운터 표시 + 네이티브 입력 제한
    'minlength' => null,
    'size' => 'lg',        // md(rounded-lg·body-2·조밀) | lg(rounded-xl·body-1·기본) — x-input과 결 통일
])

@php
    $id = $attributes->get('id') ?? $name ?? 'textarea-' . uniqid();
    $hasError = filled($error);
    $counter = filled($maxlength);
    $initial = trim((string) $slot);

    $sizeCls = $size === 'md' ? 'rounded-lg px-3.5 py-2.5 text-body-2' : 'rounded-xl px-4 py-3 text-body-1';
    // 폼 기본(lg)은 라벨을 body-1(16px)로 — 시니어 가독성. 조밀한 md는 14px 유지.
    $labelCls = $size === 'lg' ? 'text-body-1' : 'text-label-1';
    $base = "w-full $sizeCls bg-background-normal text-label-normal placeholder:text-label-assistive resize-y shadow-elevation-xs border transition-colors duration-150 focus:outline-none disabled:bg-interaction-disable disabled:text-label-disable";
    $state = $hasError
        ? 'border-status-negative focus:border-status-negative focus:ring-2 focus:ring-status-negative/30'
        : 'border-line-solid-normal focus:border-primary focus:ring-2 focus:ring-primary/30';
    $fieldClasses = trim("$base $state");
    // footer 행: hint/error(좌) 또는 카운터(우) 중 하나라도 있으면 렌더
    $hasFooter = $hasError || filled($hint) || $counter;
@endphp

<div class="flex flex-col gap-1.5"
     @if ($counter) x-data="{ v: @js($initial) }" x-modelable="v" {{ $attributes->whereStartsWith('x-model') }} @endif>
    @if ($label)
        <label for="{{ $id }}" class="{{ $labelCls }} font-medium text-label-neutral">
            {{ $label }}
            @if ($required)<span class="text-status-negative">*</span>@endif
        </label>
    @endif

    @if ($counter)
        <textarea
            id="{{ $id }}"
            rows="{{ $rows }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($required) required @endif
            maxlength="{{ $maxlength }}"
            @if ($minlength) minlength="{{ $minlength }}" @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-msg" @endif
            x-model="v"
            {{ $attributes->whereDoesntStartWith('x-model')->class($fieldClasses) }}></textarea>
    @else
        <textarea
            id="{{ $id }}"
            rows="{{ $rows }}"
            @if ($name) name="{{ $name }}" @endif
            @if ($required) required @endif
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-msg" @endif
            {{ $attributes->class($fieldClasses) }}>{{ $slot }}</textarea>
    @endif

    @if ($hasFooter)
        <div class="flex items-start justify-between gap-2">
            <p id="{{ $id }}-msg"
               class="flex-1 text-label-2 {{ $hasError ? 'text-status-negative' : 'text-label-assistive' }}">
                {{ $hasError ? $error : $hint }}
            </p>
            @if ($counter)
                <span class="shrink-0 text-label-2 font-medium text-label-alternative">
                    <span x-text="v.length"></span>/{{ $maxlength }}
                </span>
            @endif
        </div>
    @endif
</div>
