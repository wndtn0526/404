{{-- 전역 저장·취소 확인 모달 — x-layout에 x-toast와 함께 1회 마운트. 어디서든 이벤트로 호출:
       window.dispatchEvent(new CustomEvent('confirm', { detail: {
           title: '이대로 게시글을 저장할까요?',      // 필수 권장 (질문형)
           message: '저장하면 …',                     // 선택 — 없으면 제목만
           confirmLabel: '저장하기',                   // 기본 '확인'
           cancelLabel: '취소하기',                    // 기본 '취소하기'
           onConfirm: () => { ... },                  // 확인 클릭 시 실행 (취소·ESC·백드롭은 아무 것도 실행 안 함)
           footerText: '아직 청담원 회원이 아니신가요?',  // 선택 — 버튼 아래 보조 안내
           footerLinkLabel: '회원가입하기',               // 선택 — footerText 옆 링크 라벨
           footerHref: '/signup',                         // 선택 — 링크 이동 경로
       } }))
     · 닫힘 애니메이션 중 문구 유지(내용을 비우지 않음 — 페이드아웃 깜빡임 방지)
     · overflow 락은 열 때 저장 → 닫을 때 복원(다른 모달 안에서 열려도 안전) --}}
<div x-data="{
        show: false, title: '', message: '', highlight: '', icon: '', confirmLabel: '확인', cancelLabel: '취소하기', onConfirm: null, onCancel: null,
        footerText: '', footerLinkLabel: '', footerHref: '', dismissible: false, rows: [], _ov: '',
        open(d) {
            this.title = d.title ?? '계속 진행할까요?';
            this.message = d.message ?? '';
            this.highlight = d.highlight ?? '';   // 강조 값(전화번호·금액 등) — 제목과 본문 사이 강조 박스로 표시
            this.icon = d.icon ?? '';             // 상단 아이콘 (사전 등록: check·send·message — 런타임 동적 로드 불가라 화이트리스트)
            this.confirmLabel = d.confirmLabel ?? '확인';
            // cancelLabel: null → 알림형(확인 단일 버튼) — 완료 안내 등 취소 개념이 없는 다이얼로그
            this.cancelLabel = d.cancelLabel === null ? null : (d.cancelLabel ?? '취소하기');
            this.onConfirm = typeof d.onConfirm === 'function' ? d.onConfirm : null;
            this.onCancel = typeof d.onCancel === 'function' ? d.onCancel : null;   // 취소·ESC·백드롭 시 실행(선택) — 스위치 원복 등
            // 버튼 아래 보조 안내 + 링크 (로그인 유도 확인창의 '회원가입하기' 등)
            this.footerText = d.footerText ?? '';
            this.footerLinkLabel = d.footerLinkLabel ?? '';
            this.footerHref = d.footerHref ?? '';
            // dismissible: true → 우상단 X 닫기 (취소 버튼 없이 CTA 하나로 두는 초대형 모달 — 로그인 유도 등)
            this.dismissible = d.dismissible === true;
            // rows: [[라벨, 값], ...] → 라벨-값 요약 카드 (접수 완료 등)
            this.rows = Array.isArray(d.rows) ? d.rows : [];
            this.show = true;
        },
        ok() { this.show = false; this.onConfirm && this.onConfirm(); },
        close() { if (! this.show) return; this.show = false; this.onCancel && this.onCancel(); },
     }"
     x-init="window.addEventListener('confirm', (e) => open(e.detail ?? {}))"
     x-effect="show ? (_ov = document.documentElement.style.overflow, document.documentElement.style.overflow = 'hidden') : (document.documentElement.style.overflow = _ov)"
     x-show="show" x-cloak @keydown.escape.window="close()"
     class="fixed inset-0 z-[90] flex items-center justify-center p-5">
    {{-- 백드롭 --}}
    <div x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="close()" class="absolute inset-0 bg-inverse-background/40"></div>
    {{-- 패널 (컴팩트 확인 다이얼로그) --}}
    <div x-show="show" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
         role="dialog" aria-modal="true" :aria-label="title"
         class="relative w-full max-w-[420px] rounded-2xl bg-background-normal p-8 shadow-elevation-xl">
        {{-- 우상단 X 닫기 (dismissible) --}}
        <button x-show="dismissible" x-cloak type="button" @click="close()" aria-label="닫기"
                class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-lg text-label-alternative transition-colors hover:bg-fill-alternative">
            <x-icon-close class="h-[22px] w-[22px]" />
        </button>
        {{-- 상단 아이콘(선택) — 아이콘·highlight 있는 다이얼로그는 전체 중앙 정렬(모먼트형), 그 외 일반 확인창은 좌측 --}}
        <span x-show="icon" x-cloak class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
            <x-icon-circle-check x-show="icon === 'check'" class="h-7 w-7 text-primary" />
            <x-icon-send x-show="icon === 'send'" class="h-7 w-7 text-primary" />
            <x-icon-message x-show="icon === 'message'" class="h-7 w-7 text-primary" />
        </span>
        <h2 class="text-heading-1 font-bold text-label-strong" :class="(highlight || icon) && 'text-center'" x-text="title"></h2>
        {{-- 강조 박스 — 사용자가 꼭 확인해야 할 핵심 값(전화번호·금액 등)을 크게 --}}
        <p x-show="highlight" x-cloak class="mt-5 rounded-xl bg-background-alternative py-4 text-center text-title-3 font-bold tabular-nums tracking-wide text-label-strong" x-text="highlight"></p>
        {{-- message는 \n 줄바꿈 지원(whitespace-pre-line) — 변경내역·미리보기 등 여러 줄 본문용 --}}
        {{-- 본문은 확인창 공통 body-1 (시니어 가독성 하한) --}}
        <p x-show="message" class="whitespace-pre-line break-keep text-body-1 leading-relaxed text-label-alternative" :class="(highlight || icon) ? 'mt-3 text-center' : 'mt-2'" x-text="message"></p>
        {{-- 라벨-값 요약 카드 (접수번호·연락처 등) — 항상 좌우 정렬 --}}
        <div x-show="rows.length" x-cloak class="mt-5 flex flex-col gap-2.5 rounded-xl border border-line-solid-neutral bg-background-alternative px-5 py-4">
            <template x-for="row in rows" :key="row[0]">
                <div class="flex items-start justify-between gap-4 text-body-2">
                    <span class="shrink-0 text-label-alternative" x-text="row[0]"></span>
                    <span class="text-right font-semibold text-label-strong" x-text="row[1]"></span>
                </div>
            </template>
        </div>
        <div class="mt-6 flex gap-2.5">
            <template x-if="cancelLabel">
                <x-button variant="secondary" size="md" type="button" class="flex-1" @click="close()"><span x-text="cancelLabel">취소하기</span></x-button>
            </template>
            <x-button variant="primary" size="md" type="button" class="flex-1" @click="ok()"><span x-text="confirmLabel">확인</span></x-button>
        </div>
        {{-- 하단 보조 안내 + 링크 (로그인 유도 확인창의 '회원가입하기' 등) --}}
        <p x-show="footerText || footerLinkLabel" x-cloak class="mt-5 text-center text-body-2 text-label-alternative">
            <span x-show="footerText" x-text="footerText"></span>
            <a x-show="footerLinkLabel" :href="footerHref" class="ml-1 font-semibold text-primary underline underline-offset-2 hover:no-underline" x-text="footerLinkLabel"></a>
        </p>
    </div>
</div>
