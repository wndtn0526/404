{{-- DS Table 셀. 기본 td (헤더 셀은 as="th").
     align=left|center|right · tone=default|muted|strong · nowrap · width. --}}
@props([
    'as' => 'td',
    'align' => 'left',
    'tone' => 'default',    // default(label-normal) | muted(label-alternative) | strong(label-strong·semibold)
    'nowrap' => false,
    'width' => null,
])

<{{ $as }}
    @if ($as === 'th') scope="col" @endif
    @if ($width) style="width: {{ $width }}" @endif
    {{ $attributes->class([
        'px-4 py-[18px] text-body-2 align-middle first:pl-5 last:pr-5',
        'text-label-normal' => $tone === 'default',
        'text-label-alternative' => $tone === 'muted',
        'font-semibold text-label-strong' => $tone === 'strong',
        'text-left' => $align === 'left',
        'text-center' => $align === 'center',
        'text-right' => $align === 'right',
        'whitespace-nowrap' => $nowrap,
    ]) }}
>{{ $slot }}</{{ $as }}>
