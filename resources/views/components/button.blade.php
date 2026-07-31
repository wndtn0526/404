@props([
    'variant' => 'primary', // primary | secondary | subtle | outline | ghost | danger
    'size' => 'md', // sm(h-10) | md(h-12) | lg(h-14)
    'href' => null, // 지정 시 <a> 로 렌더
    'type' => 'button', // <button> 일 때 type
    'icon' => null, // 앞쪽 아이콘 이름 (blade-icons, prefix 'icon')
    'iconTrailing' => null, // 뒤쪽 아이콘 이름
    'block' => false, // 전체 너비
    'disabled' => false,
])

@php
    $base =
        'inline-flex items-center justify-center gap-2 font-sans font-semibold rounded-xl transition-colors duration-150 select-none focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 disabled:cursor-not-allowed';

    $variants = [
        'primary' =>
            'bg-primary text-white hover:bg-primary-strong active:bg-primary-heavy disabled:bg-interaction-disable disabled:text-label-disable',
        'secondary' =>
            'bg-fill-normal text-label-normal hover:bg-fill-strong active:bg-fill-strong disabled:bg-interaction-disable disabled:text-label-disable',
        // subtle: 평상시 흰 배경·회색 텍스트(저강도) → 호버 시 fill로 채워짐. 보조/대체 액션용.
        'subtle' =>
            'bg-background-normal text-label-alternative hover:bg-fill-normal hover:text-label-normal active:bg-fill-strong disabled:text-label-disable',
        'outline' =>
            'border border-line-solid-normal text-label-normal bg-background-normal hover:bg-fill-alternative active:bg-fill-normal disabled:text-label-disable disabled:border-line-solid-alternative',
        'ghost' =>
            'text-label-normal hover:bg-fill-alternative active:bg-fill-normal disabled:text-label-disable',
        // danger: 반려·삭제 등 파괴적 액션 전용. 청담원 DS 에는 없고 전자결재에서 추가했다.
        // 반려를 primary 버튼으로 내보내면 승인과 구분이 안 돼서 사고가 난다.
        'danger' =>
            'bg-status-negative text-white hover:bg-accent-fg-red active:bg-accent-fg-red disabled:bg-interaction-disable disabled:text-label-disable',
    ];

    $sizes = [
        'sm' => 'h-10 px-4 text-body-2',
        'md' => 'h-12 px-5 text-body-1',
        'lg' => 'h-14 px-6 text-headline-1',
    ];

    $iconSizes = ['sm' => 'w-4 h-4', 'md' => 'w-5 h-5', 'lg' => 'w-5 h-5'];

    $classes = implode(' ', [
        $base,
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $block ? 'w-full' : '',
    ]);

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href)
        href="{{ $disabled ? '#' : $href }}"
        @if ($disabled) aria-disabled="true" tabindex="-1" @endif
    @else
        type="{{ $type }}"
        @if ($disabled) disabled @endif
    @endif
    {{ $attributes->class($classes) }}
>
    @if ($icon)
        <x-dynamic-component :component="'icon-' . $icon" :class="$iconSizes[$size] ?? 'w-5 h-5'" />
    @endif

    {{ $slot }}

    @if ($iconTrailing)
        <x-dynamic-component :component="'icon-' . $iconTrailing" :class="$iconSizes[$size] ?? 'w-5 h-5'" />
    @endif
</{{ $tag }}>
