{{-- DS 스탯 타일 — 대시보드 KPI 카드.
     마이페이지(구직자) 대시보드 + 백오피스 홈 등에서 공용으로 사용.
       <x-stat-tile label="지원 중" value="3" unit="건" sub="이번 주 +2" />
       <x-stat-tile label="올해 목표 수급자" value="12" unit="/ 15명" :progress="80" sub="달성률 80% · 전월 +1명" />
       <x-stat-tile label="이번 달 운영 여력" value="+645" unit="만원"
                    note="공단 청구 4,825 − 인건비 4,180만원" sub="사회복지사 1명 충원해도 +325만원 흑자" />
     구성(위→아래): label(회색) → value(24px bold)+unit → progress 바(선택) → note(회색 디테일·선택) → sub(primary 강조)
     기본 슬롯은 sub 아래 추가 커스텀 영역. --}}
@props([
    'label' => null,
    'value' => null,
    'unit' => null,
    'sub' => null,            // 강조 보조줄 (takeaway)
    'subTone' => 'primary',   // sub 색: primary(teal) | positive | cautionary | negative
    'note' => null,           // 회색 보조 디테일줄 (value~sub 사이)
    'progress' => null,       // 0~100 진행바 (지정 시 노출)
])

@php
    $subToneClass = [
        'primary' => 'text-primary-strong',
        'positive' => 'text-status-positive',
        'cautionary' => 'text-status-cautionary',
        'negative' => 'text-status-negative',
    ][$subTone] ?? 'text-primary-strong';
@endphp

<div {{ $attributes->class('flex flex-col gap-3 rounded-md border border-line-solid-neutral bg-background-normal px-[18px] py-4') }}>
    @if ($label)
        <p class="text-body-2 font-medium text-label-alternative">{{ $label }}</p>
    @endif

    @if ($value !== null)
        <p class="flex items-baseline gap-1">
            <span class="text-title-3 font-bold text-label-strong">{{ $value }}</span>
            @if ($unit)<span class="text-body-2 font-medium text-label-alternative">{{ $unit }}</span>@endif
        </p>
    @endif

    @if ($progress !== null)
        <div class="h-1.5 w-full overflow-hidden rounded-full bg-fill-normal">
            <div class="h-full rounded-full bg-primary" style="width:{{ max(0, min(100, (int) $progress)) }}%"></div>
        </div>
    @endif

    @if ($note)
        <p class="text-label-2 text-label-assistive">{{ $note }}</p>
    @endif

    @if ($sub)
        <p class="text-body-2 font-medium {{ $subToneClass }}">{{ $sub }}</p>
    @endif

    {{ $slot }}
</div>
