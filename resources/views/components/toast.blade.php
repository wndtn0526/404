{{-- 전역 토스트(SnackBar) — Figma GPRO_PORTFOLIO 'SnackBars' (node 1002-114358).

     두 가지로 부른다
       1) 세션 success/error 플래시를 자동으로 읽는다 (폼 POST + 리다이렉트 흐름)
       2) window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }))

     x-layout 이 한 번만 심는다. 화면마다 마크업을 두지 않는다.

     원본 실측 — 335x48 · 면 Mono/900 · 반경 6 · px 20 · pt 11 pb 12 · 칸 사이 6
                 아이콘 18(초록 원 + 흰 체크) 위에서 3 · 글자 15 lh23 흰색 위에서 2
                 자리는 GNB(56) 바로 아래 가운데

     ⚠️ 전에는 면 자체가 초록/빨강이었다. 원본은 면이 늘 검고 아이콘만 색이 바뀐다.
        문구가 길어져도 화면이 초록 판으로 덮이지 않는다.
     ⚠️ 원본에는 닫기 버튼이 없다. 4초 뒤 저절로 사라지고 막는 것도 없어서 그대로 뒀다.
     ⚠️ 실패용 아이콘은 원본에 없다. DS circle-exclamation 을 빨강으로 쓴다 — 성공(채운 원)과
        결이 다르다. 디자이너가 실패 스낵바를 그리면 그 글리프로 바꾼다. --}}
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
     {{-- 3) 다음 화면에서 띄울 문구 — sessionStorage 에 넣고 옮겨 가면 도착한 화면이 띄운다.
            신청을 마치고 문서 신청 목록으로 보내는 흐름이 이걸 쓴다(node 1002-114271).
            서버 세션 플래시가 정석이지만 이 저장소는 아직 보낼 곳이 없고 정적 배포본으로도 나간다.
            ⚠️ 붙일 때는 세션 플래시로 옮긴다. 그때 이 조각은 지운다. --}}
     x-init="
        if (show) timer = setTimeout(() => show = false, 4000);
        const pending = sessionStorage.getItem('cdw.toast');
        if (pending) { sessionStorage.removeItem('cdw.toast'); $nextTick(() => display(pending)); }
        window.addEventListener('toast', (e) => display(e.detail.message, e.detail.type === 'error'));
     "
     x-show="show" x-cloak
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
     {{-- 폭은 내용에 맞춘다(w-max) — 긴 문구도 화면 한계 전까지 한 줄이고, 좁은 화면에서만 접힌다.
          top-14 는 GNB 높이 56 이다. 원본이 GNB 바로 아래 붙는다. --}}
     class="fixed left-1/2 top-14 z-[100] flex w-max max-w-[calc(100vw-2.5rem)] -translate-x-1/2 items-start gap-1.5 rounded-lg bg-mono-900 px-5 pt-[11px] pb-3 shadow-elevation-lg"
     role="status" aria-live="polite">
    <span class="flex shrink-0 pt-[3px]">
        {{-- 성공 — 원본 success_18 을 그대로 옮긴 확장 아이콘. 원이 currentColor 다. --}}
        <x-ext-success-fill x-show="! isError" class="size-[18px] text-status-positive" />
        <x-icon-circle-exclamation x-show="isError" class="size-[18px] text-status-negative" />
    </span>
    {{-- whitespace-pre-line: 메시지의 \n 을 줄바꿈으로 낸다 · break-keep: 한국어 어절 단위 --}}
    <p class="min-w-0 flex-1 whitespace-pre-line break-keep pt-0.5 text-body-2 leading-[23px] text-white" x-text="message"></p>
</div>
