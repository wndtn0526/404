{{-- 전역 입력 모달 — 네이티브 prompt() 대체 (피드백 위계 §4: 무음 · DS 컴포넌트만). x-layout에 toast·confirm과 함께 1회 마운트.
       window.dispatchEvent(new CustomEvent('prompt', { detail: {
           title: '그룹 이름을 변경할까요?',        // 질문형 권장
           message: '…',                            // 선택 — 안내 한 줄
           label: '그룹 이름',                       // 입력 필드 라벨(선택)
           placeholder: '이름 입력',                 // 선택
           value: '현재값',                          // 초기값(선택)
           confirmLabel: '변경하기',                  // 기본 '확인'
           onConfirm: (value) => { ... },            // 확인 시 입력값과 함께 실행 (빈값이면 비활성)
       } }))
     · 취소·ESC·백드롭은 실행 없음 · Enter = 확인 --}}
<div x-data="{
        show: false, title: '', message: '', label: '', placeholder: '', value: '', confirmLabel: '확인', onConfirm: null, _ov: '',
        open(d) {
            this.title = d.title ?? '값을 입력해 주세요.';
            this.message = d.message ?? '';
            this.label = d.label ?? '';
            this.placeholder = d.placeholder ?? '';
            this.value = d.value ?? '';
            this.confirmLabel = d.confirmLabel ?? '확인';
            this.onConfirm = typeof d.onConfirm === 'function' ? d.onConfirm : null;
            this.show = true;
            this.$nextTick(() => this.$refs.input?.focus());
        },
        ok() { if (! this.value.trim()) return; this.show = false; this.onConfirm && this.onConfirm(this.value.trim()); },
     }"
     x-init="window.addEventListener('prompt', (e) => open(e.detail ?? {}))"
     x-effect="show ? (_ov = document.documentElement.style.overflow, document.documentElement.style.overflow = 'hidden') : (document.documentElement.style.overflow = _ov)"
     x-show="show" x-cloak @keydown.escape.window="show = false"
     class="fixed inset-0 z-[90] flex items-center justify-center p-5">
    <div x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="show = false" class="absolute inset-0 bg-inverse-background/40"></div>
    <div x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
         role="dialog" aria-modal="true" :aria-label="title"
         class="relative w-full max-w-[420px] rounded-2xl bg-background-normal p-8 shadow-elevation-xl">
        <h2 class="text-heading-1 font-bold text-label-strong" x-text="title"></h2>
        <p x-show="message" class="mt-2 whitespace-pre-line break-keep text-body-1 leading-relaxed text-label-alternative" x-text="message"></p>
        <div class="mt-5">
            <label x-show="label" class="mb-1.5 block text-label-1 font-semibold text-label-neutral" x-text="label"></label>
            <input type="text" x-ref="input" x-model="value" :placeholder="placeholder" @keydown.enter.prevent="ok()"
                   class="h-12 w-full rounded-xl border border-line-solid-normal bg-background-normal px-4 text-body-1 text-label-normal placeholder:text-label-assistive transition-colors focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
        </div>
        <div class="mt-6 flex gap-2.5">
            <x-button variant="secondary" size="md" type="button" class="flex-1" @click="show = false">취소하기</x-button>
            <x-button variant="primary" size="md" type="button" class="flex-1" ::class="! value.trim() && 'pointer-events-none opacity-40'" @click="ok()"><span x-text="confirmLabel">확인</span></x-button>
        </div>
    </div>
</div>
