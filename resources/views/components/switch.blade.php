@props([
    'label' => null,
    'name' => null,
    'value' => '1',
    'size' => 'md',        // sm | md  (Figma: Small / Medium)
    'checked' => false,
    'disabled' => false,
])

@php
    $id = $attributes->get('id') ?? $name ?? 'switch-' . uniqid();

    // 트랙(track) / 노브(thumb) 치수 — Figma Switch
    $tracks = [
        'md' => 'h-8 w-[52px] p-1',
        'sm' => 'h-6 w-10 p-[3px]',
    ];
    $thumbs = [
        'md' => 'size-6',
        'sm' => 'size-[18px]',
    ];
    // 노브 이동: thumb은 input의 형제가 아니라 트랙에 중첩돼 있어 peer-checked가 직접 닿지 않음.
    // 트랙에서 자식 노브(>span)를 타겟해야 동작한다.
    $moves = [
        'md' => 'peer-checked:[&>span]:translate-x-5',
        'sm' => 'peer-checked:[&>span]:translate-x-4',
    ];

    $track = $tracks[$size] ?? $tracks['md'];
    $thumb = $thumbs[$size] ?? $thumbs['md'];
    $move = $moves[$size] ?? $moves['md'];

    // x-model 바인딩 시 체크박스에 Alpine 스코프가 필요 → 루트에 x-data 부여
    $bindsModel = $attributes->whereStartsWith('x-model')->isNotEmpty();
@endphp

<label for="{{ $id }}" @if ($bindsModel) x-data @endif {{ $attributes->whereDoesntStartWith('x-model')->class('inline-flex items-center gap-3 select-none ' . ($disabled ? 'cursor-not-allowed' : 'cursor-pointer')) }}>
    <input
        type="checkbox"
        id="{{ $id }}"
        @if ($name) name="{{ $name }}" @endif
        value="{{ $value }}"
        @checked($checked)
        @disabled($disabled)
        {{ $attributes->whereStartsWith('x-model') }}
        class="peer sr-only"
    />

    <span class="relative inline-flex shrink-0 items-center rounded-full bg-fill-strong transition-colors duration-200
                 peer-checked:bg-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40
                 peer-disabled:opacity-[0.43] {{ $track }} {{ $move }}">
        <span class="pointer-events-none inline-block rounded-full bg-white transition-transform duration-200 {{ $thumb }}"></span>
    </span>

    @if ($label)
        <span class="text-body-2 text-label-neutral peer-disabled:text-label-disable">{{ $label }}</span>
    @endif
</label>
