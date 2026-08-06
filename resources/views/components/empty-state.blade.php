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

     props:
       lines  : 문구. 배열이면 줄마다 <p> 하나 — 원본이 두 줄이라 줄바꿈을 데이터로 받는다
       action : 버튼 글자. 없으면 버튼을 내지 않는다
       href   : 버튼 링크. 없으면 <button> 으로 나간다 --}}
@props([
    'lines' => [],
    'action' => null,
    'href' => null,
])

<div {{ $attributes->class('flex min-w-0 flex-col items-center px-5 text-center') }}>
    @foreach ((array) $lines as $line)
        <p class="text-body-2 leading-[23px] text-warm-gray-500">{{ $line }}</p>
    @endforeach

    @if (filled($action))
        <x-button variant="primary" size="sm" :href="$href" class="mt-[15px] shrink-0">{{ $action }}</x-button>
    @endif
</div>
