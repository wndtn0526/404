{{-- 원본 Breadcrumb — Figma 디자인 가이드 Tab (1002:522322)
     현재 위치의 경로. 마지막 항목이 현재 페이지이며 링크가 아니다.

     props:
       items : [['label' => '결재함', 'href' => '/approvals'], ['label' => '상신 문서']]
               href 가 없거나 마지막 항목이면 링크 없이 현재 위치로 렌더한다.
     원본 실측: 14px · 항목 간격 6~8px · 경로 warm gray 600 · 현재 위치는 진한 검정. --}}
@props([
    'items' => [],
])

@php
    $items = array_values(array_filter((array) $items));
    $last = count($items) - 1;
@endphp

@if ($items)
    <nav {{ $attributes->merge(['aria-label' => '현재 위치']) }}>
        <ol class="flex flex-wrap items-center gap-1.5">
            @foreach ($items as $i => $item)
                @php
                    $label = is_array($item) ? ($item['label'] ?? '') : $item;
                    $href = is_array($item) ? ($item['href'] ?? null) : null;
                    $isCurrent = $i === $last;
                @endphp

                <li class="flex items-center gap-1.5">
                    @if ($href && ! $isCurrent)
                        <a href="{{ $href }}"
                           class="text-label-1 text-label-alternative transition-colors hover:text-label-normal">{{ $label }}</a>
                    @else
                        <span class="text-label-1 text-label-normal @if ($isCurrent) font-semibold @endif"
                              @if ($isCurrent) aria-current="page" @endif>{{ $label }}</span>
                    @endif

                    @unless ($isCurrent)
                        <x-icon-chevron-right class="h-3.5 w-3.5 shrink-0 text-label-assistive" aria-hidden="true" />
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
