{{-- 그룹 항목 한 줄 — 피드 탭 추천, 그룹 탭의 가입한 그룹·추천에서 같은 모양으로 쓴다.
     Figma node 1104-58981 실측: 썸네일 48 · 반경 4 · 이름 15px Bold · 설명 15px · 참여수 13px Warm gray/500

     ⚠️ 썸네일은 원본이 Nike·Framer·Apple 로고와 사진이다. 타사 상표·스톡 사진이라
        이니셜 타일로 뒀다. 배경은 완성된 클래스명을 담는다(Tailwind 문자열 스캔).

     $group = ['name' =>, 'desc' =>, 'members' =>, 'tone' =>, 'initial' =>]
     $first = 첫 항목이면 위 구분선을 그리지 않는다 --}}
<li @class([
    'flex min-w-0 items-start gap-5 py-6',
    'border-t border-warm-gray-100' => ! ($first ?? false),
    'pt-0' => $first ?? false,
])>
    <span class="flex size-12 shrink-0 items-center justify-center rounded-md {{ $group['tone'] }} text-caption-1 font-bold text-white"
          aria-hidden="true">{{ $group['initial'] }}</span>

    <div class="min-w-0 flex-1">
        <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $group['name'] }}</p>
        <p class="truncate pt-[5px] text-body-2 leading-[23px] text-mono-black">{{ $group['desc'] }}</p>
        <p class="pt-[5px] text-label-2 leading-5 text-warm-gray-500">
            <span class="tabular-nums">{{ $group['members'] }}</span>명 참여중
        </p>
    </div>
</li>
