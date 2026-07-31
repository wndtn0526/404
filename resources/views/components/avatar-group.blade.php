{{-- DS Avatar Group — 그룹화된 다수의 아바타 (Figma 16215:26148 · Montage Avatar Group).
     겹쳐 쌓인 아바타 + 초과 인원 「외 N명」. 이름 배열을 받아 이니셜로 표시(person variant).
       <x-avatar-group :names="['김나현','이경석','정다은']" size="xs" :max="4" />
     size: xs(24px·1.5px 링·-6px 겹침) | sm(32px·2px 링·-8px 겹침). trailing 「외 N명」=label-1. --}}
@props([
    'names' => [],
    'size' => 'xs',   // xs | sm
    'max' => 4,       // 초과 시 「외 N명」
])

@php
    use App\Support\Avatar;

    $names = array_values(array_filter($names));
    $shown = array_slice($names, 0, $max);
    $extra = max(0, count($names) - $max);

    $sizes = [
        'xs' => ['av' => 'h-6 w-6 text-caption-2', 'ring' => 'border-[1.5px]', 'over' => '-space-x-1.5'],
        'sm' => ['av' => 'h-8 w-8 text-caption-1', 'ring' => 'border-2',       'over' => '-space-x-2'],
    ];
    $s = $sizes[$size] ?? $sizes['xs'];
@endphp

@if (count($names))
    <div {{ $attributes->class('inline-flex items-center gap-2') }}>
        <div class="flex items-center {{ $s['over'] }}">
            @foreach ($shown as $n)
                <span class="relative inline-flex shrink-0 items-center justify-center rounded-full border-background-normal font-bold {{ Avatar::solid($n) }} {{ $s['av'] }} {{ $s['ring'] }}">{{ mb_substr($n, 0, 1) }}</span>
            @endforeach
        </div>
        @if ($extra > 0)
            <span class="whitespace-nowrap text-label-1 font-semibold text-label-alternative">외 {{ $extra }}명</span>
        @endif
    </div>
@endif
