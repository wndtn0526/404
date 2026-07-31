{{-- 파일 첨부 드롭존 (DS) — 드래그앤드롭 + 클릭 업로드 + 선택 파일 표시/삭제
     · 박스로 끌어다 놓거나 클릭해 파일 선택 → 하단에 DS file-item 칩으로 표시(삭제 가능)
     · 드롭 시 input.files 에 반영되어 폼 제출에 포함됨
     props:
       name     : input name (multiple 시 자동으로 name[] 처리)
       accept   : 허용 MIME/확장자 (기본 이미지·PDF)
       multiple : 다중 첨부 허용
       title    : 안내 문구
       hint     : 보조 설명 (허용 형식·용량 등)
       action   : 버튼 라벨
       error    : 에러(negative) 메시지 --}}
@props([
    'name' => null,
    'accept' => 'image/*,application/pdf',
    'multiple' => false,
    'title' => '파일을 끌어다 놓거나 클릭해 업로드',
    'hint' => null,
    'action' => '파일 선택',
    'error' => null,
])

@php $hasError = filled($error); @endphp

<div x-data="dsFileDropzone({ multiple: {{ $multiple ? 'true' : 'false' }} })"
     {{ $attributes->class('flex flex-col gap-3') }}>

    {{-- hidden file input — 드롭존 클릭 div 바깥에 배치해 click 이벤트 재귀 방지 --}}
    <input x-ref="input" type="file" class="sr-only"
           @if ($name) name="{{ $multiple ? $name . '[]' : $name }}" @endif
           @if ($multiple) multiple @endif
           accept="{{ $accept }}" @change="onSelect($event)">

    {{-- 드롭존 — @click 으로 $refs.input.click() 직접 트리거 --}}
    <div @dragover.prevent="drag = true"
         @dragleave.prevent="drag = false"
         @drop.prevent="onDrop($event)"
         @click="$refs.input.click()"
         @keydown.enter.prevent="$refs.input.click()"
         @keydown.space.prevent="$refs.input.click()"
         role="button"
         tabindex="0"
         aria-label="{{ $title }}"
         class="flex cursor-pointer flex-col items-center justify-center rounded-2xl px-6 py-9 text-center transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
         :class="drag
             ? 'bg-primary-soft ring-2 ring-primary'
             : '{{ $hasError ? 'ring-1 ring-status-negative/40' : '' }} bg-background-alternative hover:bg-fill-normal'">
        <p class="text-body-2 font-semibold text-label-normal">{{ $title }}</p>
        @if ($hint)
            <p class="mt-1.5 text-label-1 text-label-alternative">{{ $hint }}</p>
        @endif
        <x-button variant="outline" size="sm" type="button" class="pointer-events-none mt-4" tabindex="-1">{{ $action }}</x-button>
    </div>

    {{-- 선택된 파일 — DS file-item 스타일 칩 --}}
    <template x-for="(f, i) in files" :key="f.key">
        <div class="flex items-center gap-3 rounded-xl border border-line-solid-neutral bg-background-normal p-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-status-negative/10 text-caption-1 font-bold text-status-negative" x-text="f.ext"></span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-body-2 font-medium text-label-normal" x-text="f.name"></p>
                <p class="mt-0.5 text-label-2 text-label-alternative" x-text="f.size"></p>
            </div>
            <button type="button" @click="remove(i)" class="shrink-0 text-label-alternative transition-colors hover:text-label-normal" aria-label="첨부 삭제">
                <x-icon-circle-close class="h-[22px] w-[22px]" />
            </button>
        </div>
    </template>

    @if ($hasError)
        <p class="text-label-2 text-status-negative">{{ $error }}</p>
    @endif
</div>

@once
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dsFileDropzone', ({ multiple = false } = {}) => ({
            files: [],
            drag: false,
            _seq: 0,
            human(b) {
                if (b == null) return '';
                const u = ['B', 'KB', 'MB', 'GB'];
                let n = b, i = 0;
                while (n >= 1024 && i < u.length - 1) { n /= 1024; i++; }
                return (i === 0 ? n : n.toFixed(1)) + u[i];
            },
            pick(list) {
                const arr = Array.from(list || []);
                if (!arr.length) return;
                const mapped = arr.map((f) => ({
                    key: ++this._seq + ':' + f.name,
                    name: f.name,
                    ext: ((f.name.split('.').pop() || '').toUpperCase().slice(0, 4)) || 'FILE',
                    size: this.human(f.size),
                }));
                this.files = multiple ? [...this.files, ...mapped] : mapped.slice(-1);
            },
            onSelect(e) { this.pick(e.target.files); },
            onDrop(e) {
                this.drag = false;
                const inp = this.$refs.input;
                if (inp && e.dataTransfer?.files.length) {
                    try { inp.files = e.dataTransfer.files; } catch (_) {}
                }
                this.pick(e.dataTransfer?.files ?? null);
            },
            remove(i) {
                this.files.splice(i, 1);
                if (!this.files.length && this.$refs.input) this.$refs.input.value = '';
            },
        }));
    });
</script>
@endpush
@endonce
