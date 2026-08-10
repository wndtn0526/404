{{-- DS Table 헤더(thead) — 회색 면 + 13px 라벨. (Figma 99:17984)
     · 데이터: :columns="['구분','제목',['label'=>'조회수','align'=>'right','width'=>'110px']]"
     · 슬롯:   <x-table.head><x-table.cell as="th">…</x-table.cell></x-table.head>
     · 선택:   selectable + :all-ids="$ids" → 맨 앞에 전체선택 체크박스 셀 --}}
@props([
    'columns' => null,       // 문자열 또는 ['label'=>, 'align'=>'left|center|right', 'width'=>] 배열
    'selectable' => false,   // 전체선택 체크박스 컬럼
    'allIds' => [],          // selectable일 때 전체 행 id 배열(전체선택 대상)
    'dense' => false,        // 원본 Row Heights 28 — x-table.cell 의 dense 와 짝이다
])

@php $ids = array_map('strval', $allIds); @endphp

<thead class="border-b border-line-solid-normal bg-background-alternative">
    <tr>
        @if ($selectable)
            <th scope="col" class="h-14 w-12 px-5 align-middle">
                {{-- DS 체크박스(흰 체크): 전체=체크 / 일부=대시 --}}
                <label class="relative inline-flex cursor-pointer items-center justify-center align-middle">
                    <input type="checkbox" aria-label="전체 선택" class="peer sr-only"
                           @change="selected = $event.target.checked ? @js($ids) : []"
                           x-effect="$el.checked = selected.length === {{ count($ids) }} && selected.length > 0; $el.indeterminate = selected.length > 0 && selected.length < {{ count($ids) }}">
                    <span class="h-5 w-5 rounded-xs border-[1.5px] border-line-normal-strong bg-background-normal transition-colors duration-150 peer-checked:border-primary peer-checked:bg-primary peer-indeterminate:border-primary peer-indeterminate:bg-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40"></span>
                    <x-icon-check class="pointer-events-none absolute h-[90%] w-[90%] text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100 [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" />
                    <span aria-hidden="true" class="pointer-events-none absolute h-0.5 w-2.5 rounded-full bg-white opacity-0 peer-indeterminate:opacity-100"></span>
                </label>
            </th>
        @endif
        @if ($columns !== null)
            @foreach ($columns as $col)
                @php
                    $label = is_array($col) ? ($col['label'] ?? '') : $col;
                    $align = is_array($col) ? ($col['align'] ?? 'left') : 'left';
                    $width = is_array($col) ? ($col['width'] ?? null) : null;
                @endphp
                <th scope="col"
                    @if ($width) style="width: {{ $width }}" @endif
                    @class([
                        'px-4 align-middle text-label-2 font-medium text-label-alternative whitespace-nowrap',
                        'h-14 first:pl-5 last:pr-5' => ! $dense,
                        'h-7 first:pl-[30px]' => $dense,
                        'text-left' => $align === 'left',
                        'text-center' => $align === 'center',
                        'text-right' => $align === 'right',
                    ])>{{ $label }}</th>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </tr>
</thead>
