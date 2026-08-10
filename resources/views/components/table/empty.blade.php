{{-- 표가 비었을 때 보이는 한 행 — Figma GPRO_PORTFOLIO node 1002-274654 실측.
     전체 열을 가로지르는 셀 하나 · 가운데 정렬 · 글자 Warm gray/400 (= --color-label-assistive).

     ⚠️ 빈 목록을 x-empty-state 로 대신하지 않는다. 원본은 헤더를 남긴 채 표 안에서 알린다 —
        어떤 열이 있는 표인지 보이는 편이 낫다고 본 것 같다. 화면 전체가 비는 자리
        (포스팅 없음 같은)에는 그대로 x-empty-state 를 쓴다.

     colspan 은 헤더 열 수와 맞춘다. 체크박스 열(selectable)도 한 칸으로 센다. --}}
@props(['colspan' => 1])

<tr {{ $attributes->class('border-b border-line-solid-alternative last:border-b-0') }}>
    <td colspan="{{ $colspan }}" class="h-14 px-4 text-center text-body-2 text-label-assistive">
        {{ $slot->isEmpty() ? '아직 변경 이력이 없습니다.' : $slot }}
    </td>
</tr>
