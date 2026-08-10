{{-- 비어 있는 상태 — Figma GPRO_PORTFOLIO node 1104-59420 "포스팅 없는 경우".

     원본 실측(375) — 문구 15 Regular · lh23 · -0.6 · Warm gray/500 · 두 줄 가운데 정렬
                      (DS body-2 와 정확히 일치)
                      문구 아래 15 · 버튼 62x36 · 반경 4 · 좌우패딩 14 · 글자 13 Bold · 검정
                      블록 중심이 탭 구분선에서 182 아래 (문구 위쪽 여백 159)

     ⚠️ 버튼은 DS <x-button size="sm"> 이다. 원본은 36 높이 · 좌우 14 · 글자 13 인데
        DS sm 은 40 · 16 · 15 다. 버튼을 손으로 만들지 않는 규칙(CLAUDE.md)을 따랐다.
        원본 버튼 배경은 Mono/Black, DS primary 는 Warm gray/900 이다.
     ⚠️ 위쪽 여백은 이 컴포넌트가 갖지 않는다. 어디에 놓이는지에 따라 달라지므로
        부르는 쪽에서 준다(퍼블릭 스페이스 피드는 pt-[159px]).

     넓은 화면(확인할 문서 node 1002-106604)은 그림 + 굵은 한 줄 + 설명 한 줄로 나온다.
     그쪽 실측 — 그림 54x67 · 그림 아래 31 · 제목 18 Bold lh27 -0.6 (DS headline-2) 검정
                 제목 아래 12 · 설명 14 Regular lh20 -0.2 · Warm gray/500
     ⚠️ 설명 줄은 모바일 원본(15)에 맞춰 둔 DS body-2 로 나간다. 넓은 화면 원본은 14 라
        한 단계 크다. 한 컴포넌트가 두 화면을 받는 자리라 줄 크기를 갈라 두지 않았다.

     props:
       icon    : ext 아이콘 이름(resources/svg/ext). 주면 문구 위에 그림을 놓는다.
       title   : 굵은 한 줄. 주면 lines 위에 온다.
       lines   : 문구. 배열이면 줄마다 <p> 하나 — 원본이 두 줄이라 줄바꿈을 데이터로 받는다
       action  : 버튼 글자. 없으면 버튼을 내지 않는다
       href    : 버튼 링크. 없으면 <button> 으로 나간다 --}}
@props([
    'icon' => null,
    'title' => null,
    'lines' => [],
    'action' => null,
    'href' => null,
])

<div {{ $attributes->class('flex min-w-0 flex-col items-center px-5 text-center') }}>
    @if ($icon)
        {{-- ⚠️ 이 그림은 두 가지 회색(Warm gray/200 · 300)으로 그려져 있어 currentColor 로
             바꿀 수 없다. Figma 에서 받은 색을 그대로 둔다. --}}
        <x-dynamic-component :component="'ext-' . $icon" class="mb-[31px] h-[67px] w-[54px]" />
    @endif

    @if (filled($title))
        <p class="pb-3 text-headline-2 font-bold leading-[27px] text-mono-black">{{ $title }}</p>
    @endif

    @foreach ((array) $lines as $line)
        <p class="text-body-2 leading-[23px] text-warm-gray-500">{{ $line }}</p>
    @endforeach

    @if (filled($action))
        <x-button variant="primary" size="sm" :href="$href" class="mt-[15px] shrink-0">{{ $action }}</x-button>
    @endif
</div>
