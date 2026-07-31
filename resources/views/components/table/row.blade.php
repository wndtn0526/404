{{-- DS Table 데이터 행(tr). (Figma 99:18003)
     · highlight = 정적 강조(연분홍) 행
     · selectable + :value = 맨 앞 체크박스 셀, 선택 시 연분홍 하이라이트(동적) --}}
@props([
    'highlight' => false,   // 정적 강조 행(연분홍 배경)
    'hover' => true,        // 호버 배경
    'selectable' => false,  // 체크박스 선택 셀
    'value' => null,        // selectable일 때 행 식별값(전체선택/선택 배열에 사용)
])

<tr {{ $attributes->class([
        'border-b border-line-solid-alternative last:border-b-0 transition-colors',
        'bg-inverse-primary/50 hover:bg-inverse-primary' => $highlight,
        'hover:bg-fill-alternative' => ! $highlight && ! $selectable && $hover,
    ]) }}
    @if ($selectable)
        :class="selected.includes(@js((string) $value)) ? 'bg-inverse-primary/50' : 'hover:bg-fill-alternative'"
    @endif>
    @if ($selectable)
        <td class="w-12 px-5 py-[18px] align-middle">
            {{-- DS 체크박스(x-checkbox와 동일): 체크 시 primary 배경 + 흰색 체크 --}}
            <label class="relative inline-flex cursor-pointer items-center justify-center align-middle">
                <input type="checkbox" value="{{ $value }}" x-model="selected" aria-label="행 선택" class="peer sr-only">
                <span class="h-5 w-5 rounded-md border-[1.5px] border-line-normal-strong bg-background-normal transition-colors duration-150 peer-checked:border-primary peer-checked:bg-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40"></span>
                <x-icon-check class="pointer-events-none absolute h-[90%] w-[90%] text-white opacity-0 transition-opacity duration-150 peer-checked:opacity-100 [&_path]:[fill-opacity:1] [&_path]:[stroke:currentColor] [&_path]:[stroke-width:1.5] [&_path]:[stroke-linejoin:round]" />
            </label>
        </td>
    @endif
    {{ $slot }}
</tr>
