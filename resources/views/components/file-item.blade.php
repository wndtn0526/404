{{-- 첨부 파일 행 — DS Figma: 업로드 완료 파일 미리보기
     props:
       name      : 파일명
       ext       : 확장자 배지 텍스트 (PDF·JPG 등)
       size      : 파일 크기 (예: '1.2MB')
       status    : 상태 문구 (기본 '업로드 완료')
       removable : 삭제(X) 버튼 노출 여부 --}}
@props([
    'name' => null,
    'ext' => 'PDF',
    'size' => null,
    'status' => '업로드 완료',
    'removable' => true,
])

@php
    $meta = implode(' · ', array_filter([$size, $status]));
@endphp

<div {{ $attributes->class('flex items-center gap-3 rounded-xl border border-line-solid-neutral bg-background-normal p-3') }}>
    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-status-negative/10 text-caption-1 font-bold text-status-negative">{{ $ext }}</span>
    <div class="min-w-0 flex-1">
        <p class="truncate text-body-2 font-medium text-label-normal">{{ $name }}</p>
        @if (filled($meta))
            <p class="mt-0.5 text-label-2 text-label-alternative">{{ $meta }}</p>
        @endif
    </div>
    @if ($removable)
        <button type="button" class="shrink-0 text-label-alternative transition-colors hover:text-label-normal" aria-label="첨부 삭제">
            <x-icon-circle-close class="h-[22px] w-[22px]" />
        </button>
    @endif
</div>
