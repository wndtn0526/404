{{-- DS Modal — 중앙 다이얼로그 (Figma 249:40025 경력 추가 등)
     이벤트 기반 개폐: 트리거에서 $dispatch('open-modal', '{name}') 으로 열고,
     ESC·백드롭 클릭·취소 버튼(open=false)으로 닫음. body 레벨 fixed 오버레이.

     props:
       name     : 트리거와 매칭되는 식별자 (필수)
       title    : 제목 (선택)
       subtitle : 제목 아래 보조 설명 (선택)
       maxWidth : 패널 최대 너비 Tailwind 클래스 (기본 max-w-[480px])

     slots:
       (default) : 본문
       footer    : 하단 액션 영역(취소/확인 등). 슬롯 안에서 open=false 로 닫기 가능.

     사용:
       <x-button @click="$dispatch('open-modal', 'career')">경력 추가</x-button>
       <x-modal name="career" title="경력 추가" subtitle="...">
           ...본문...
           <x-slot:footer>
               <x-button variant="outline" block @click="open = false">취소</x-button>
               <x-button variant="primary" block>추가하기</x-button>
           </x-slot:footer>
       </x-modal> --}}
@props([
    'name',
    'title' => null,
    'subtitle' => null,
    'maxWidth' => 'max-w-[480px]',
    'closeButton' => false,   // 우상단 X 닫기 버튼 표시
    'scroll' => false,        // 긴 내용: 헤더(제목·X)·푸터 고정, 본문만 스크롤
])

<div x-data="{ open: false }"
     x-on:open-modal.window="$event.detail === '{{ $name }}' && (open = true)"
     x-on:keydown.escape.window="open = false"
     x-effect="document.documentElement.style.overflow = open ? 'hidden' : ''"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-5">
    {{-- 백드롭 --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="open = false"
         class="absolute inset-0 bg-inverse-background/50"></div>

    {{-- 패널 --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
         role="dialog" aria-modal="true" @if ($title) aria-label="{{ $title }}" @endif
         @if ($scroll)
             {{ $attributes->class('relative flex max-h-full w-full ' . $maxWidth . ' flex-col overflow-hidden rounded-md bg-background-normal shadow-elevation-xl') }}
         @else
             {{ $attributes->class('relative w-full ' . $maxWidth . ' rounded-md bg-background-normal p-7 shadow-elevation-xl') }}
         @endif>
        @if ($closeButton)
            <button type="button" @click="open = false"
                    class="absolute right-6 top-6 z-10 inline-flex h-8 w-8 items-center justify-center rounded-full bg-background-normal text-label-alternative transition-colors hover:bg-fill-alternative hover:text-label-normal"
                    aria-label="닫기">
                <x-icon-close class="h-5 w-5" />
            </button>
        @endif

        @if ($scroll)
            {{-- 고정 헤더 --}}
            @if ($title || $subtitle)
                <div class="shrink-0 flex flex-col gap-1.5 px-7 pb-4 pt-7 {{ $closeButton ? 'pr-16' : '' }}">
                    @if ($title)<h2 class="text-heading-1 font-bold text-label-strong">{{ $title }}</h2>@endif
                    @if ($subtitle)<p class="text-body-2 text-label-alternative">{{ $subtitle }}</p>@endif
                </div>
            @endif

            {{-- 스크롤 본문 (하단 패딩으로 마지막 필드와 푸터 사이 간격 확보) --}}
            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain px-7 {{ ($title || $subtitle) ? '' : 'pt-7' }} pb-6">
                {{ $slot }}
            </div>

            {{-- 고정 푸터 --}}
            @isset($footer)
                <div class="shrink-0 flex items-center gap-3 border-t border-line-solid-alternative px-7 py-4">
                    {{ $footer }}
                </div>
            @endisset
        @else
            @if ($title || $subtitle)
                <div class="mb-6 flex flex-col gap-1.5">
                    @if ($title)
                        <h2 class="text-heading-1 font-bold text-label-strong">{{ $title }}</h2>
                    @endif
                    @if ($subtitle)
                        <p class="text-body-2 text-label-alternative">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif

            {{ $slot }}

            @isset($footer)
                <div class="mt-7 flex items-center gap-3">
                    {{ $footer }}
                </div>
            @endisset
        @endif
    </div>
</div>
