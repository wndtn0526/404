{{-- DS Pagination — 데이터 테이블 하단 페이지네이션 (기본 10건/페이지).
     정적 UI: 현재 페이지·건수 범위 표시 + 번호/이전·다음. 실제 페이지 이동(재조회)은 화면에서 연동.
     반응형: 데스크톱=건수 + 번호(가운데 '…' 생략) / 모바일=건수 + 이전·현재/전체·다음으로 축약.
     props: total(전체 건수) · perPage(페이지당, 기본 10) · current(현재 페이지, 기본 1) · unit(기본 '건') --}}
@props([
    'total' => 0,
    'perPage' => 10,
    'current' => 1,
    'unit' => '건',
    'pageParam' => null,       // 설정 시 실제 링크 생성 (GET 파라미터명 지정). null=정적 버튼
    'perPageOptions' => [],    // 지정 시 우측에 「N개씩 보기」 개수 선택 노출 (예: [10,50,100])
    'sum' => null,             // 지정 시 좌측 건수 뒤에 「(합계 …)」 표기 (금액 테이블용)
])

@php
    $perPage = max(1, (int) $perPage);
    $pages = max(1, (int) ceil($total / $perPage));
    $current = min(max(1, (int) $current), $pages);
    $start = $total === 0 ? 0 : ($current - 1) * $perPage + 1;
    $end = min($current * $perPage, $total);

    // 표시 페이지 번호: 처음/끝 + 현재 ±1, 끊기는 구간은 '…'
    $nums = [];
    $prev = 0;
    for ($i = 1; $i <= $pages; $i++) {
        if ($i === 1 || $i === $pages || abs($i - $current) <= 1) {
            if ($prev && $i - $prev > 1) {
                $nums[] = '…';
            }
            $nums[] = $i;
            $prev = $i;
        }
    }

    $numBtn = 'inline-flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-label-1 font-medium transition-colors';
    $navBtn = 'inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-label-neutral transition-colors hover:bg-fill-alternative disabled:cursor-not-allowed disabled:text-label-disable disabled:hover:bg-transparent';
@endphp

<nav {{ $attributes->class('flex flex-wrap items-center justify-between gap-3') }} aria-label="페이지네이션">
    {{-- 건수 범위 (+ 합계) --}}
    <p class="text-label-2 text-label-alternative">
        <span class="font-semibold text-label-normal">{{ number_format($start) }}–{{ number_format($end) }}</span>
        / 총 {{ number_format($total) }}{{ $unit }}
        @if ($sum !== null)<span class="ml-0.5">(합계 {{ $sum }})</span>@endif
    </p>

    {{-- 우측: 개수 선택 + 컨트롤 --}}
    <div class="flex items-center gap-2 sm:gap-4">
        {{-- 페이지당 개수 (N개씩 보기) — 텍스트 + 쉐브론(테두리 없음) --}}
        @if (! empty($perPageOptions))
            <div class="relative" x-data="{ open: false, per: {{ $perPage }} }" @click.outside="open = false" @keydown.escape="open = false">
                <button type="button" @click="open = ! open" :aria-expanded="open" aria-haspopup="menu"
                        class="inline-flex items-center gap-1 whitespace-nowrap text-label-1 font-medium text-label-normal transition-colors hover:text-label-strong">
                    <span x-text="per.toLocaleString() + '개씩 보기'"></span>
                    <x-icon-chevron-down class="h-4 w-4 text-label-alternative transition-transform duration-200" ::class="open && 'rotate-180'" />
                </button>
                <div x-show="open" x-cloak x-transition.origin.bottom.duration.150ms role="menu"
                     class="absolute bottom-full right-0 z-20 mb-1.5 min-w-[140px] overflow-hidden rounded-xl border border-line-solid-neutral bg-background-normal p-1 shadow-elevation-lg">
                    @foreach ($perPageOptions as $opt)
                        <button type="button" role="menuitem" @click="per = {{ (int) $opt }}; open = false"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-label-1 transition-colors hover:bg-fill-alternative"
                                :class="per === {{ (int) $opt }} ? 'font-semibold text-primary' : 'text-label-normal'">
                            <span>{{ number_format($opt) }}개씩 보기</span>
                            <x-icon-check class="h-4 w-4" x-show="per === {{ (int) $opt }}" />
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

    {{-- 컨트롤 --}}
    <div class="flex items-center gap-1">
        @if ($pageParam && $current > 1)
            <a href="{{ request()->fullUrlWithQuery([$pageParam => $current - 1]) }}" class="{{ $navBtn }}" aria-label="이전 페이지"><x-icon-chevron-left class="h-5 w-5" /></a>
        @else
            <button type="button" class="{{ $navBtn }}" @disabled($current <= 1) aria-label="이전 페이지"><x-icon-chevron-left class="h-5 w-5" /></button>
        @endif

        {{-- 데스크톱: 페이지 번호 --}}
        <div class="hidden items-center gap-1 sm:flex">
            @foreach ($nums as $n)
                @if ($n === '…')
                    <span class="inline-flex h-9 w-7 items-center justify-center text-label-1 text-label-assistive">…</span>
                @elseif ($pageParam)
                    <a href="{{ request()->fullUrlWithQuery([$pageParam => $n]) }}" aria-label="{{ $n }} 페이지"
                       @if ($n === $current) aria-current="page" @endif
                       class="{{ $numBtn }} {{ $n === $current ? 'font-bold text-primary pointer-events-none' : 'text-label-neutral hover:bg-fill-alternative' }}">{{ $n }}</a>
                @else
                    <button type="button" aria-label="{{ $n }} 페이지" @if ($n === $current) aria-current="page" @endif
                            class="{{ $numBtn }} {{ $n === $current ? 'font-bold text-primary' : 'text-label-neutral hover:bg-fill-alternative' }}">{{ $n }}</button>
                @endif
            @endforeach
        </div>

        {{-- 모바일: 현재/전체 --}}
        <span class="px-2.5 text-label-1 font-medium text-label-normal sm:hidden">{{ $current }} <span class="text-label-assistive">/ {{ $pages }}</span></span>

        @if ($pageParam && $current < $pages)
            <a href="{{ request()->fullUrlWithQuery([$pageParam => $current + 1]) }}" class="{{ $navBtn }}" aria-label="다음 페이지"><x-icon-chevron-right class="h-5 w-5" /></a>
        @else
            <button type="button" class="{{ $navBtn }}" @disabled($current >= $pages) aria-label="다음 페이지"><x-icon-chevron-right class="h-5 w-5" /></button>
        @endif
    </div>
    </div>
</nav>
