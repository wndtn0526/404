{{-- 프로필 우측 사이드 486 — 소개 · 관심사 · 오늘의 아티클 추천.
     커리어 탭과 그룹 탭이 같은 구성을 쓴다 (Figma node 1104-58476 · 1104-58981).

     원본 실측 — 카드 반경 6 · 내부 패딩 30 · 제목 상단 17
                 소개·관심사 제목 20px Bold · lh 30 (DS heading-2)
                 아티클 추천 제목 18px Bold · lh 27 (DS headline-2) + 우측 더보기
                 아티클 썸네일 120

     ⚠️ 아티클 썸네일은 원본이 스톡 스크린샷이다. 토큰 색 면으로 대신했다.

     $profile · $career · $articles 를 부모 스코프에서 받는다. --}}
<aside class="hidden w-[486px] shrink-0 flex-col gap-6 xl:flex">

    {{-- 소개 --}}
    <section class="rounded-lg bg-background-normal px-[30px] pb-[30px] pt-[17px]">
        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">소개</h2>
        <p class="pt-[26px] text-body-2 leading-[23px] text-mono-black">{{ $profile['bio'] }}</p>
        <p class="text-body-2 leading-[23px] text-mono-black">유용한 정보들 함께 공유해요!</p>
    </section>

    {{-- 관심사 --}}
    <section class="rounded-lg bg-background-normal px-[30px] pb-[30px] pt-[17px]">
        <h2 class="text-heading-2 font-bold leading-[30px] text-mono-black">관심사</h2>
        <div class="flex flex-wrap gap-2 pt-[26px]">
            @foreach ($career['interests'] as $interest)
                <x-chip size="medium">{{ $interest }}</x-chip>
            @endforeach
        </div>
    </section>

    {{-- 오늘의 아티클 추천 — 피드 탭과 같은 카드다 --}}
    <section class="rounded-lg bg-background-normal p-[30px]">
        <div class="flex items-baseline justify-between">
            <h2 class="text-headline-2 font-bold leading-[27px] text-mono-black">오늘의 아티클 추천</h2>
            <a href="#" class="text-body-2 leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">더보기</a>
        </div>

        <div class="flex flex-col gap-[40px] pt-[37px]">
            @foreach ($articles as $article)
                <a href="#" class="flex items-start gap-5 transition-opacity hover:opacity-70">
                    <span class="size-[120px] shrink-0 rounded-md {{ $article['tone'] }}" aria-hidden="true"></span>
                    <span class="flex min-w-0 flex-1 flex-col">
                        <span class="text-body-2 font-bold leading-[23px] text-mono-black">{{ $article['title'] }}</span>
                        <span class="pt-[10px] text-body-2 leading-[23px] text-mono-black">{{ $article['desc'] }}</span>
                        <span class="flex items-center gap-1.5 pt-[14px]">
                            <span class="size-5 shrink-0 rounded-full bg-warm-gray-200" aria-hidden="true"></span>
                            <span class="truncate text-label-2 font-medium leading-5 text-warm-gray-500">{{ $article['source'] }}</span>
                        </span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
</aside>
