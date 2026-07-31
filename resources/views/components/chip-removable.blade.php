{{-- 삭제 가능한 칩 그룹 — X 누르면 사라지고, '+추가' 버튼 포함. Alpine 반응형.
     <x-chip-removable :items="['요양보호사 1급', ...]" add-label="추가" /> --}}
@props([
    'items' => [],
    'addLabel' => '추가',
])

<div x-data="{ items: @js(array_values($items)) }" class="mt-3 flex flex-wrap items-center gap-2">
    <template x-for="(item, i) in items" :key="item">
        <span class="inline-flex items-center gap-1 rounded-[10px] border border-primary/[0.43] bg-primary/5 py-[7px] pl-[11px] pr-2 text-body-2 font-medium text-primary">
            <span x-text="item"></span>
            <button type="button" @click="items.splice(i, 1)"
                    class="inline-flex shrink-0 items-center justify-center rounded-full p-0.5 text-primary/60 transition-colors hover:bg-primary/10 hover:text-primary"
                    :aria-label="item + ' 삭제'">
                <x-icon-close class="h-3.5 w-3.5" />
            </button>
        </span>
    </template>
    <button type="button"
            class="inline-flex items-center gap-1 rounded-[10px] border border-line-normal-neutral px-[11px] py-[7px] text-body-2 font-medium text-label-alternative transition-colors hover:bg-fill-alternative">
        <x-icon-plus class="h-3.5 w-3.5" />{{ $addLabel }}
    </button>
</div>
