{{-- 한 칸에 여러 컨트롤 — Figma GPRO_PORTFOLIO 기안 작성(node 1002-113842) '계좌 정보'.
     테두리 하나 안에 은행 드롭다운 + 세로 구분선 + 계좌번호 입력이 들어간다.

     원본 실측 — 칸 높이 32(sm) · 반경 4 · 테두리 Warm gray/200
                 구분선 1px 세로 전체 · 안쪽 컨트롤은 테두리 없음

     안쪽에는 DS 컨트롤을 variant="bare" 로 넣는다. 테두리·반경은 이 칸이 그리고,
     컨트롤은 글자와 캐럿만 낸다. 컨트롤을 직접 만들지 않기 위한 장치다.

     ⚠️ 포커스 테두리는 칸 전체에 걸린다(focus-within). 안쪽 어느 칸을 누르든
        원본처럼 칸 하나가 통째로 살아난다.
     ⚠️ 구분선은 divide-x 로 그린다 — 직계 자식 사이에만 들어가므로 칸마다
        <div> 로 한 번 감싼다.

     props
       label : 칸 위 라벨. x-input 과 같은 자리·같은 글씨다.
       size  : sm(32) | md·lg(40) — 안쪽 컨트롤의 size 와 맞춘다.
       for   : 라벨이 가리킬 id (안쪽 첫 컨트롤)

     사용:
       <x-field-group label="계좌 정보" size="sm">
           <div class="w-[128px] shrink-0">
               <x-dropdown variant="bare" size="sm" … />
           </div>
           <div class="min-w-0 flex-1">
               <x-input variant="bare" size="sm" … />
           </div>
       </x-field-group> --}}
@props([
    'label' => null,
    'size' => 'sm',
    'for' => null,
])

@php
    $heights = ['sm' => 'h-8', 'md' => 'h-10', 'lg' => 'h-10'];
    $labelCls = $size === 'lg' ? 'text-body-1' : 'text-label-1';
@endphp

<div {{ $attributes->class('flex min-w-0 flex-col gap-1.5') }}>
    @if ($label)
        <label @if ($for) for="{{ $for }}" @endif class="{{ $labelCls }} font-medium text-label-neutral">{{ $label }}</label>
    @endif

    <div class="flex min-w-0 items-stretch divide-x divide-line-solid-normal overflow-hidden rounded-md border border-line-solid-normal bg-background-normal transition-colors duration-150 focus-within:border-deep-blue-900 {{ $heights[$size] ?? $heights['sm'] }}">
        {{ $slot }}
    </div>
</div>
