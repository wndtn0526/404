{{-- DS 세그먼트 컨트롤 — 두 개 이상 옵션 중 하나 선택(예/아니오 등 토글).
     선택값은 hidden input 으로 제출. x-model 패스스루(x-modelable)로 부모 상태 바인딩 가능.
     props:
       name     : hidden input name (폼 제출용)
       options  : [value => label] 연관배열 (예: ['Y'=>'예','N'=>'아니오'])
       selected : 초기 선택값
       size     : sm | md
       block    : 전체 너비(옵션 균등 분할) --}}
@props([
    'name' => null,
    'options' => [],
    'selected' => null,
    'size' => 'md',
    'block' => false,
])

@php
    $selectedStr = $selected === null ? '' : (string) $selected;
    $opts = [];
    foreach ($options as $val => $lbl) {
        $opts[] = ['value' => (string) $val, 'label' => $lbl];
    }

    $sizes = [
        'sm' => ['pad' => 'p-0.5', 'btn' => 'px-3 py-1 rounded-md text-label-2'],
        'md' => ['pad' => 'p-1',   'btn' => 'px-4 py-1.5 rounded-md text-label-1'],
    ];
    $sz = $sizes[$size] ?? $sizes['md'];
@endphp

<div x-data="{ value: @js($selectedStr) }"
     x-modelable="value"
     {{ $attributes->whereStartsWith('x-model') }}
     role="radiogroup"
     {{ $attributes->whereDoesntStartWith('x-model')->class(($block ? 'flex w-full' : 'inline-flex') . ' items-center gap-1 rounded-md bg-fill-alternative ' . $sz['pad']) }}>
    @if ($name)
        <input type="hidden" name="{{ $name }}" :value="value">
    @endif
    @foreach ($opts as $o)
        <button type="button" role="radio"
                :aria-checked="value === @js($o['value'])"
                @click="value = @js($o['value'])"
                class="{{ $block ? 'flex-1 ' : '' }}{{ $sz['btn'] }} font-medium transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                :class="value === @js($o['value'])
                    ? 'bg-background-normal text-label-normal'
                    : 'text-label-alternative hover:text-label-normal'">
            {{ $o['label'] }}
        </button>
    @endforeach
</div>
