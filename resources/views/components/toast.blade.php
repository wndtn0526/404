{{-- 전역 토스트 — 1) 세션 success/error 플래시를 자동으로 읽어 표시(폼 POST+리다이렉트 흐름용)
     2) window.dispatchEvent(new CustomEvent('toast', {detail:{message, type}})) 로 어디서든 즉시 호출 가능(fetch/AJAX 흐름용)
     x-layout에 한 번만 심어두면 개별 페이지에서 매번 마크업을 반복할 필요 없음. 4초 후 자동 닫힘 + 수동 닫기 버튼. --}}
@php
    $flashMessage = session('error') ?? session('success');
    $flashIsError = (bool) session('error');
@endphp
<div x-data="{
        show: {{ $flashMessage ? 'true' : 'false' }},
        message: @js($flashMessage ?? ''),
        isError: {{ $flashIsError ? 'true' : 'false' }},
        timer: null,
        display(message, isError = false) {
            clearTimeout(this.timer);
            this.message = message;
            this.isError = isError;
            this.show = true;
            this.timer = setTimeout(() => this.show = false, 4000);
        },
     }"
     x-init="
        if (show) timer = setTimeout(() => show = false, 4000);
        window.addEventListener('toast', (e) => display(e.detail.message, e.detail.type === 'error'));
     "
     x-show="show" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
     {{-- 폭은 내용에 맞춤(w-max) — 긴 문구도 화면 한계 전까지 한 줄 유지, 모바일에서만 줄바꿈 --}}
     class="fixed left-1/2 top-5 z-[100] flex w-max max-w-[calc(100vw-2.5rem)] -translate-x-1/2 items-center gap-2.5 rounded-lg px-5 py-3 shadow-elevation-lg"
     :class="isError ? 'bg-status-negative' : 'bg-status-positive'"
     role="status" aria-live="polite">
    <x-icon-circle-exclamation x-show="isError" class="h-5 w-5 shrink-0 text-white" />
    <x-icon-circle-check x-show="! isError" class="h-5 w-5 shrink-0 text-white" />
    {{-- whitespace-pre-line: 메시지의 \n을 줄바꿈으로 렌더(문장 단위 개행 제어) · break-keep: 한국어 어절 단위 줄바꿈 --}}
    <p class="flex-1 whitespace-pre-line break-keep text-body-1 font-medium text-white" x-text="message"></p>
    <button type="button" @click="show = false" aria-label="닫기" class="shrink-0 text-white/70 transition-colors hover:text-white">
        <x-icon-close class="h-4 w-4" />
    </button>
</div>
