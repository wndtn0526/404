{{-- 입력 칸 아래 요약 줄 — Figma GPRO_PORTFOLIO 'success text' (node I1002:108543;2759:106028).
     "시간 정보를 입력하면 박스 하단에 총 시간과 날짜가 노출되어 사용자가 더블 체크할 수 있습니다."
     (원본 주석 node 1002-108552)

     원본 실측 — 입력 아래 5 · 아이콘 12(채운 초록 원 + 흰 체크) · 글자 12 Medium Primary/green 900
                아이콘 위 여백 2 · 아이콘과 글자 사이 5

     $expr : 문구를 만드는 Alpine 식. 값이 비면 줄 자체가 서지 않는다. --}}
<template x-if="{{ $expr }}">
    <p class="mt-[5px] flex min-w-0 items-start gap-[5px] text-caption-1 font-medium leading-[18px] text-status-positive">
        <x-ext-success-fill class="mt-0.5 size-3 shrink-0" />
        <span class="min-w-0" x-text="{{ $expr }}"></span>
    </p>
</template>
