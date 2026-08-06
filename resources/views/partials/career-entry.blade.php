{{-- 커리어 항목 한 줄 — 경력과 학력이 같은 모양이라 하나로 뺐다.

     PC  실측 (node 1104-58476) — 로고 48 · 반경 4 · 제목 15 Bold lh23 · 기관 15 lh23
                                  기간 13 Warm gray/400 · 간격 16 · 줄 사이 3/5
     모바일 실측 (node 1104-59365) — 로고 42 · 반경 4 · 제목 13 Bold lh20 · 기관 13 lh20
                                  기간 13 Warm gray/500 · 간격 12 · 줄 사이 4 (세 줄 24 간격)

     ⚠️ 로고는 원본이 네이버·워크앤조이·하버드 이미지다. 타사 상표라 이니셜 타일로 뒀다.
        원본 모바일 타일에는 검정 3% 외곽선이 있다. 이니셜 타일은 배경이 진해서
        외곽선이 보이지 않으므로 넣지 않았다. 실제 로고 이미지가 붙으면 같이 넣는다.

     $entry = ['initial' =>, 'tone' =>, 'title' =>, 'org' =>, 'period' =>] --}}
<li class="flex min-w-0 items-start gap-3 lg:gap-4">
    <span class="flex size-[42px] shrink-0 items-center justify-center rounded-md {{ $entry['tone'] }} text-body-2 font-bold text-white lg:size-12"
          aria-hidden="true">{{ $entry['initial'] }}</span>

    <div class="min-w-0 flex-1 lg:pt-0.5">
        <p class="truncate text-label-2 font-bold leading-5 text-mono-black lg:text-body-2 lg:leading-[23px]">{{ $entry['title'] }}</p>
        <p class="truncate pt-1 text-label-2 leading-5 text-mono-black lg:pt-[3px] lg:text-body-2 lg:leading-[23px]">{{ $entry['org'] }}</p>
        <p class="truncate pt-1 text-label-2 leading-5 text-warm-gray-500 lg:pt-[5px] lg:text-warm-gray-400">{{ $entry['period'] }}</p>
    </div>
</li>
