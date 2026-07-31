{{-- DS 탭(언더라인) — Montage Tab(Figma 99:21282). 목적별 콘텐츠 구분/필터용.
     활성 = label-strong + 하단 2px 보더 · 비활성 = label-assistive. 전체 폭 하단 디바이더.
     선택값은 hidden input 제출 + x-model 패스스루(x-modelable)로 부모 상태 바인딩.
     props:
       name     : hidden input name(폼 제출용)
       options  : [value => label] 연관배열
       selected : 초기 선택값
       accent   : 활성 강조색. strong=블랙(정보 구분용 기본·Figma) | primary=코랄(필터 등 브랜드 강조)
       block    : 전체 너비(옵션 균등 분할)
       scrollable : 탭이 넘칠 때 가로 스크롤(공개 페이지·탭 많을 때). 스크롤은 바깥 래퍼에 걸고
                    touch-pan-x로 좌우 드래그만 허용(대각선/세로 드래그로 탭이 딸려 움직이는 것 방지).
                    tablist는 w-max→md:w-full(모바일 스크롤/데스크톱 풀폭 트랙). 기본 off=기존과 100% 동일. --}}
@props([
    'name' => null,
    'options' => [],
    'selected' => null,
    'accent' => 'strong',
    'block' => false,
    'scrollable' => false,
])

@php
    $selectedStr = $selected === null ? '' : (string) $selected;
    $opts = [];
    foreach ($options as $val => $lbl) {
        $opts[] = ['value' => (string) $val, 'label' => $lbl];
    }
    // 활성 강조: 텍스트는 또렷하게 label-strong 유지 · 밑줄 색만 accent로
    $activeText = 'text-label-strong';
    $lines = [
        'strong'  => 'bg-label-strong',
        'primary' => 'bg-primary',
    ];
    $activeLine = $lines[$accent] ?? $lines['strong'];
    // scrollable: tablist 폭 = 모바일 콘텐츠 폭(w-max·스크롤) → md↑ 풀폭 트랙
    $tablistWidth = $scrollable ? ' w-max md:w-full' : '';
@endphp

@if ($scrollable)
{{-- 가로 스크롤 래퍼: 밑줄 잘림 방지(overflow는 여기, tablist는 overflow visible) · touch-pan-x=좌우만 · 스크롤바 숨김 --}}
<div class="touch-pan-x select-none overflow-x-auto [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
@endif
<div x-data="{ value: @js($selectedStr) }"
     x-modelable="value"
     {{ $attributes->whereStartsWith('x-model') }}
     role="tablist"
     {{-- 회색 트랙은 컨테이너 border-b. overflow-x-auto 금지(overflow-y 강제 auto로 밑줄이 잘림) → 스크롤은 scrollable 래퍼가 담당 --}}
     {{ $attributes->whereDoesntStartWith('x-model')->class('relative flex items-center gap-6 border-b border-line-normal-alternative' . $tablistWidth) }}>
    @if ($name)
        <input type="hidden" name="{{ $name }}" :value="value">
    @endif
    @foreach ($opts as $o)
        <button type="button" role="tab"
                :aria-selected="value === @js($o['value'])"
                @click="value = @js($o['value'])"
                class="{{ $block ? 'flex-1 justify-center ' : '' }}relative flex shrink-0 items-center gap-1.5 whitespace-nowrap py-3 text-body-2 font-semibold transition-colors focus:outline-none"
                :class="value === @js($o['value']) ? '{{ $activeText }}' : 'text-label-assistive hover:text-label-alternative'">
            {{ $o['label'] }}
            {{-- 활성 밑줄: 절대배치로 회색 트랙 위에 얹어 확실히 덮음(검정/코랄 라인이 레이어 위) --}}
            <span aria-hidden="true" x-show="value === @js($o['value'])" x-cloak
                  class="absolute inset-x-0 -bottom-px h-0.5 rounded-full {{ $activeLine }}"></span>
        </button>
    @endforeach
</div>
@if ($scrollable)
</div>
@endif
