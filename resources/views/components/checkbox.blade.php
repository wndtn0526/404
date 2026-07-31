@props([
    'label' => null,
    'name' => null,
    'value' => '1',
    'checked' => false,
    'block' => false,   // 전체 너비(카드형 라벨 등)
])

@php
    $id = $attributes->get('id') ?? $name ?? 'checkbox-' . uniqid();
    $hasSlot = filled(trim($slot));
@endphp

<label for="{{ $id }}" class="{{ $block ? 'flex w-full' : 'inline-flex' }} items-center gap-2.5 cursor-pointer select-none group">
    <span class="relative flex items-center justify-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            @if ($name) name="{{ $name }}" @endif
            value="{{ $value }}"
            @checked($checked)
            {{ $attributes->class('peer sr-only') }}
        />
        <span class="w-6 h-6 rounded-md border-2 border-line-normal-strong bg-background-normal
                     transition-colors duration-150
                     peer-checked:bg-primary peer-checked:border-primary
                     peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40
                     group-hover:border-primary"></span>
        {{-- 체크: 박스 대비 90% 비율(24px 박스). 획·불투명도로 볼드감 보강. --}}
        <x-icon-check class="pointer-events-none absolute w-[90%] h-[90%] text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-150
                     [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" />
    </span>

    @if ($hasSlot)
        {{ $slot }}
    @elseif ($label)
        <span class="text-body-2 text-label-neutral">{{ $label }}</span>
    @endif
</label>
