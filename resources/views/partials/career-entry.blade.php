{{-- 커리어 항목 한 줄 — 경력과 학력이 같은 모양이라 하나로 뺐다.
     Figma node 1104-58476 실측: 로고 48 · 반경 4 · 제목 15px Bold · 기관 15px · 기간 13px Warm gray/400

     ⚠️ 로고는 원본이 네이버·워크앤조이·하버드 이미지다. 타사 상표라 이니셜 타일로 뒀다.

     $entry = ['initial' =>, 'tone' =>, 'title' =>, 'org' =>, 'period' =>] --}}
<li class="flex min-w-0 items-start gap-4">
    <span class="flex size-12 shrink-0 items-center justify-center rounded-md {{ $entry['tone'] }} text-body-2 font-bold text-white"
          aria-hidden="true">{{ $entry['initial'] }}</span>

    <div class="min-w-0 flex-1 pt-0.5">
        <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $entry['title'] }}</p>
        <p class="truncate pt-[3px] text-body-2 leading-[23px] text-mono-black">{{ $entry['org'] }}</p>
        <p class="truncate pt-[5px] text-label-2 leading-5 text-warm-gray-400">{{ $entry['period'] }}</p>
    </div>
</li>
