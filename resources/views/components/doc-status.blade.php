@props([
    'status', // App\Enums\DocumentStatus 또는 그 value 문자열('draft', 'pending', …)
    'size' => 'md', // x-badge 와 동일: xs | sm | md | lg
    'variant' => 'solid', // solid | outlined | filled
    'showIcon' => true,
])

@php
    use App\Enums\DocumentStatus;

    // 문자열이 와도 받아준다. 상태의 정의·색·아이콘은 전부 enum 이 갖고 있고
    // 이 컴포넌트는 그것을 DS 배지에 꽂아주는 역할만 한다.
    $s = $status instanceof DocumentStatus ? $status : DocumentStatus::from($status);
@endphp

<x-badge
    :size="$size"
    :variant="$variant"
    :color="$s->badgeColor()"
    :icon="$showIcon ? $s->icon() : null"
    {{ $attributes }}
>
    {{ trim($slot) ?: $s->label() }}
</x-badge>
