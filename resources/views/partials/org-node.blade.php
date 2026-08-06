{{-- 조직도 노드 한 칸 — Figma GPRO_PORTFOLIO node 1002-279525 실측.

     헤더 카드 280x82 (반경 6 · 패딩 20 · 심볼 42 · 이름 15 Bold · 전체 N명 12 Warm gray/600)
     본문 카드 280 (조직장 / 구분선 / 멤버 / 멤버 추가)
       소제목 12 Bold Warm gray/600 · 행 62 · 행 안쪽 좌우 20
       추가 버튼 40 원형 · 면 Warm gray/100 · 글자 14 Warm gray/500
       멤버 아바타 40 · 이름 15 Bold · 역할 12 Warm gray/600
       구분선 240 (좌우 20 안쪽) Warm gray/100

     ⚠️ 원본 본문 카드에는 이름 헤더가 없다. 조직이 둘 이상이면 어느 조직인지 알 수 없어
        헤더(심볼 + 이름 + 전체 N명 + 더보기)를 붙였다. 헤더 모양은 루트 카드와 같다.
     ⚠️ 접기 원(30)은 자식(하위 조직)을 접는다. 원본에서 이 원이 카드 아래 테두리에 걸쳐 있고
        그 아래 점선·+·자식 카드가 이어지는 구조다.

     변수:
       org  = ['name','total','mark'|'initial','tone','leader'=>['name','role'],
               'members'=>[['name','role'],...]]
       root = 회사(맨 위) 카드면 true — 본문(조직장·멤버)을 내지 않는다 --}}
@php
    $root = $root ?? false;
    $members = $org['members'] ?? [];
    $leader = $org['leader'] ?? null;

    $rowBase = 'flex h-[62px] min-w-0 items-center gap-3 px-5';
    $addBtn = 'flex size-10 shrink-0 items-center justify-center rounded-full bg-warm-gray-100 text-label-normal transition-colors group-hover:bg-warm-gray-200';
    $addText = 'truncate pl-0.5 text-label-1 leading-5 text-warm-gray-500 transition-colors group-hover:text-label-normal';
    $groupTitle = 'px-5 pt-5 text-caption-1 font-bold leading-[18px] text-warm-gray-600';
@endphp

{{-- ── 헤더 카드 ── --}}
<div class="w-[280px] rounded-lg bg-background-normal">
    <div class="flex min-w-0 items-center gap-2.5 p-5">
        {{-- 심볼 — 회사는 LNB 레일과 같은 브랜드 마크, 조직은 이니셜 타일.
             원본 42 · 반경 4 · 검정 3% 외곽선 --}}
        @if (! empty($org['mark']))
            <span class="flex size-[42px] shrink-0 items-center justify-center rounded-md bg-workspace-tile-teal"
                  aria-hidden="true">
                <x-dynamic-component :component="'brand-' . $org['mark']" class="h-5 w-6" />
            </span>
        @else
            <span class="flex size-[42px] shrink-0 items-center justify-center rounded-md {{ $org['tone'] ?? 'bg-deep-blue-800' }} text-body-2 font-bold text-white"
                  aria-hidden="true">{{ $org['initial'] ?? mb_substr($org['name'], 0, 1) }}</span>
        @endif

        <div class="min-w-0 flex-1">
            <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $org['name'] }}</p>
            <p class="truncate pt-px text-caption-1 leading-[18px] text-warm-gray-600">
                전체 <span class="tabular-nums">{{ $org['total'] ?? (count($members) + ($leader ? 1 : 0)) }}</span>명
            </p>
        </div>

        <button type="button"
                class="inline-flex size-6 shrink-0 items-center justify-center text-label-normal transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
                aria-label="{{ $org['name'] }} 더보기">
            <x-icon-more-vertical class="size-6" />
        </button>
    </div>
</div>

{{-- ── 본문 카드 — 조직장 · 멤버. 회사(루트)에는 내지 않는다 ── --}}
@unless ($root)
    <div class="mt-2 w-[280px] overflow-hidden rounded-lg bg-background-normal pb-2.5">
        <p class="{{ $groupTitle }}">조직장</p>

        @if ($leader)
            <div class="{{ $rowBase }}">
                <x-thumbnail :name="$leader['name']" size="md" shape="circle" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $leader['name'] }}</p>
                    <p class="truncate text-caption-1 leading-[18px] text-warm-gray-600">{{ $leader['role'] }}</p>
                </div>
            </div>
        @else
            <button type="button" class="{{ $rowBase }} group w-full text-left focus:outline-none focus-visible:bg-fill-alternative">
                <span class="{{ $addBtn }}"><x-icon-plus class="size-6" /></span>
                <span class="{{ $addText }}">조직장 추가</span>
            </button>
        @endif

        <div class="mx-5 mt-2 h-px bg-warm-gray-100" aria-hidden="true"></div>

        <p class="{{ $groupTitle }}">멤버 ({{ count($members) }})</p>

        @foreach ($members as $member)
            <div class="{{ $rowBase }}">
                <x-thumbnail :name="$member['name']" size="md" shape="circle" />
                <div class="min-w-0 flex-1">
                    <p class="truncate text-body-2 font-bold leading-[23px] text-mono-black">{{ $member['name'] }}</p>
                    <p class="truncate text-caption-1 leading-[18px] text-warm-gray-600">{{ $member['role'] }}</p>
                </div>
            </div>
        @endforeach

        <button type="button" class="{{ $rowBase }} group w-full text-left focus:outline-none focus-visible:bg-fill-alternative">
            <span class="{{ $addBtn }}"><x-icon-plus class="size-6" /></span>
            <span class="{{ $addText }}">멤버 추가</span>
        </button>
    </div>
@endunless
