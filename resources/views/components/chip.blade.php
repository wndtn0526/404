{{-- DS Action Chip — 형태는 청담원 DS Figma(16215:41950) 출신, 색·타이포는 원본 토큰
     항목을 제어/선택하거나 상태를 표시할 때 사용. 낮은 시각 위계.

     props:
       size        : xsmall | small | medium(기본) | large
       variant     : solid(기본) | outlined
       active      : 정적 활성 상태 (bool)
       activeWhen  : Alpine 표현식 문자열 — 주면 반응형 토글(button)로 렌더(폼 다중/단일 선택용)
       leadingIcon : 좌측 아이콘 (blade-icons 이름)
       trailingIcon: 우측 아이콘
       disabled    : 비활성
       as          : 렌더 태그 강제 (기본: activeWhen 있으면 button, 없으면 span)

     상태 색 토큰
       solid    off = Fill/Alternative + Label/Alternative · on = Label/Strong(검정) + Inverse/Label
       outlined off = Line/Normal/Neutral 보더 + Label/Alternative · on = Primary 5% 면 + Primary 43% 보더 + Primary 텍스트 --}}
@props([
    'size' => 'medium',
    'variant' => 'solid',
    'active' => false,
    'activeWhen' => null,
    'leadingIcon' => null,
    'trailingIcon' => null,
    'disabled' => false,
    'as' => null,
])

@php
    $sizes = [
        'xsmall' => ['box' => 'gap-0.5 px-[7px] py-1 rounded-md',            'text' => 'text-caption-1', 'icon' => 'w-3 h-3'],
        'small'  => ['box' => 'gap-0.5 px-2 py-1.5 rounded-md',              'text' => 'text-label-1',   'icon' => 'w-3.5 h-3.5'],
        'medium' => ['box' => 'gap-[3px] px-[11px] py-[7px] rounded-md', 'text' => 'text-body-2',    'icon' => 'w-3.5 h-3.5'],
        'large'  => ['box' => 'gap-[3px] px-3 py-[9px] rounded-md',      'text' => 'text-body-2',    'icon' => 'w-4 h-4'],
    ];
    $sz = $sizes[$size] ?? $sizes['medium'];

    // off 색과 hover 를 분리 — hover 는 '인터랙티브(버튼)' 칩에만 적용(정적 라벨 span 은 호버 없음)
    $states = [
        'solid' => [
            'on'    => 'bg-label-strong text-inverse-label',
            'off'   => 'bg-fill-alternative text-label-alternative',
            'hover' => 'hover:bg-fill-normal',
        ],
        'outlined' => [
            'on'    => 'border border-primary/[0.43] bg-primary/5 text-primary',
            'off'   => 'border border-line-normal-neutral text-label-alternative',
            'hover' => 'hover:bg-fill-alternative',
        ],
    ];
    $st = $states[$variant] ?? $states['solid'];

    $reactive = filled($activeWhen);
    $tag = $as ?? ($reactive ? 'button' : 'span');
    $interactive = $tag === 'button' && ! $disabled;      // 토글/액션 칩(button)일 때만 호버
    $offClass = $st['off'] . ($interactive ? ' ' . $st['hover'] : '');

    $base = implode(' ', array_filter([
        'inline-flex items-center justify-center whitespace-nowrap font-medium transition-colors',
        $sz['box'], $sz['text'],
        $reactive ? null : ($active ? $st['on'] : $offClass),
        $disabled ? 'cursor-not-allowed opacity-40' : null,
    ]));
@endphp

<{{ $tag }}
    @if ($tag === 'button') type="button" @endif
    @if ($disabled) disabled @endif
    @if ($reactive) :class="({{ $activeWhen }}) ? '{{ $st['on'] }}' : '{{ $offClass }}'" @endif
    {{ $attributes->class($base) }}
>
    @if ($leadingIcon)
        <x-dynamic-component :component="'icon-' . $leadingIcon" class="{{ $sz['icon'] }} shrink-0" />
    @endif
    <span class="px-0.5">{{ $slot }}</span>
    @if ($trailingIcon)
        <x-dynamic-component :component="'icon-' . $trailingIcon" class="{{ $sz['icon'] }} shrink-0" />
    @endif
</{{ $tag }}>
