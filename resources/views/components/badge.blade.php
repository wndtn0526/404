@props([
    'variant' => 'solid',     // solid(틴트) | outlined(보더) | filled(채움·강조)
    'size' => 'sm',           // xs | sm | md | lg   (Figma: XSmall / Small / Medium / Large)
    'color' => 'neutral',     // neutral | primary | blue | green | red | cyan | orange | violet
    'icon' => null,           // 앞 아이콘 (blade-icons, prefix 'icon')
    'iconTrailing' => null,   // 뒤 아이콘
])

@php
    // 사이즈: 패딩·라운드·타이포(토큰)·간격·아이콘크기  (Figma Content Badge)
    $sizes = [
        'xs' => ['box' => 'px-1.5 py-[3px] rounded-xs gap-0.5 text-caption-2', 'icon' => 'w-3 h-3'],
        'sm' => ['box' => 'px-1.5 py-1 rounded-xs gap-[3px] text-caption-1',   'icon' => 'w-3.5 h-3.5'],
        'md' => ['box' => 'px-2 py-[5px] rounded-xs gap-1 text-label-2',       'icon' => 'w-4 h-4'],
        'lg' => ['box' => 'px-2 py-1.5 rounded-xs gap-1 text-label-1',         'icon' => 'w-4 h-4'],
    ];

    // 색 × variant. Solid=배경 틴트(8%)·Outlined=보더(43%)·Filled=색 채움+흰 텍스트(강조). 텍스트는 해당 색.
    // (Tailwind JIT가 인식하도록 전체 클래스 문자열을 그대로 둠)
    $styles = [
        'neutral' => [
            'solid'    => 'bg-fill-normal text-label-alternative',
            'outlined' => 'border border-line-normal-neutral text-label-alternative',
            'filled'   => 'bg-fill-strong text-label-normal',
        ],
        'primary' => [
            'solid'    => 'bg-primary/[0.08] text-primary-strong',
            'outlined' => 'border border-primary/[0.43] text-primary-strong',
            'filled'   => 'bg-primary text-white',
        ],
        'blue' => [
            'solid'    => 'bg-accent-fg-blue/[0.08] text-accent-fg-blue',
            'outlined' => 'border border-accent-fg-blue/[0.43] text-accent-fg-blue',
            'filled'   => 'bg-accent-fg-blue text-white',
        ],
        'green' => [
            'solid'    => 'bg-accent-fg-green/[0.08] text-accent-fg-green',
            'outlined' => 'border border-accent-fg-green/[0.43] text-accent-fg-green',
            'filled'   => 'bg-accent-fg-green text-white',
        ],
        'red' => [
            'solid'    => 'bg-accent-fg-red/[0.08] text-accent-fg-red',
            'outlined' => 'border border-accent-fg-red/[0.43] text-accent-fg-red',
            'filled'   => 'bg-accent-fg-red text-white',
        ],
        'cyan' => [
            'solid'    => 'bg-accent-fg-cyan/[0.08] text-accent-fg-cyan',
            'outlined' => 'border border-accent-fg-cyan/[0.43] text-accent-fg-cyan',
            'filled'   => 'bg-accent-fg-cyan text-white',
        ],
        'orange' => [
            'solid'    => 'bg-accent-fg-orange/[0.08] text-accent-fg-orange',
            'outlined' => 'border border-accent-fg-orange/[0.43] text-accent-fg-orange',
            'filled'   => 'bg-accent-fg-orange text-white',
        ],
        'violet' => [
            'solid'    => 'bg-accent-fg-violet/[0.08] text-accent-fg-violet',
            'outlined' => 'border border-accent-fg-violet/[0.43] text-accent-fg-violet',
            'filled'   => 'bg-accent-fg-violet text-white',
        ],
    ];

    $s = $sizes[$size] ?? $sizes['sm'];
    $color = $styles[$color] ?? $styles['neutral'];
    $tone = $color[$variant] ?? $color['solid'];

    $classes = implode(' ', [
        'inline-flex items-center justify-center font-semibold whitespace-nowrap align-middle',
        $s['box'],
        $tone,
    ]);
@endphp

<span {{ $attributes->class($classes) }}>
    @if ($icon)
        <x-dynamic-component :component="'icon-' . $icon" :class="$s['icon']" />
    @endif
    {{ $slot }}
    @if ($iconTrailing)
        <x-dynamic-component :component="'icon-' . $iconTrailing" :class="$s['icon']" />
    @endif
</span>
