{{-- DS Avatar Group — 그룹화된 다수의 아바타 (Figma 디자인 가이드 Profile 1002:522932).
     겹쳐 쌓인 아바타 + 초과 인원. 이름 배열을 받아 머리글자로 낸다.
       <x-avatar-group :names="['김나현','이경석','정다은']" size="xs" :max="4" />

     size     : xs(24 · 1.5 링 · 6 겹침) | sm(32 · 2 링 · 8 겹침) | md(40 · 2 링 · 4 겹침)
     overflow : text(기본 · 뒤에 「외 N명」) | count(원본 · 마지막 자리에 검정 원 「+N」)
     namesExpr: 이름이 Alpine 쪽에만 있을 때 쓰는 식 (예: "asidePicked().map(m => m.name)").
                주면 names 대신 이 식으로 그린다. x-thumbnail 의 name-expr 과 같은 장치다.

     md · count 는 결재선 카드가 쓴다 — GPRO_PORTFOLIO 'Profile Group' (node I1002:115443)
     실측 184x40 (40 아바타 넷 + 검정 +N 원 하나 · 36 간격 = 4 겹침 · 흰 링 2 · 글자 12).

     ⚠️ 원본 아바타는 사진이다. 이 저장소는 다른 화면과 같이 머리글자를 쓴다.
     ⚠️ 원본 +N 원은 순검정(Mono/Black)인데 프라이머리 토큰(Black)을 따랐다.
     ⚠️ 배경은 x-thumbnail 의 폴백과 같은 deep blue 800 하나다. 전에는 이름마다 색을 고르는
        App\Support\Avatar 를 불렀는데 그 클래스가 저장소에 없다 — 쓰는 곳이 없어서 안 터졌을
        뿐, 그리는 순간 죽는 코드였다. 이름별 색이 필요해지면 그때 x-thumbnail 과 같이 넣는다. --}}
@props([
    'names' => [],
    'size' => 'xs',        // xs | sm | md
    'max' => 4,            // 초과 시 「외 N명」 또는 「+N」
    'overflow' => 'text',  // text | count
    'namesExpr' => null,
])

@php
    $names = array_values(array_filter($names));
    $shown = array_slice($names, 0, $max);
    $extra = max(0, count($names) - $max);

    $sizes = [
        'xs' => ['av' => 'h-6 w-6 text-caption-2', 'ring' => 'border-[1.5px]', 'over' => '-space-x-1.5'],
        'sm' => ['av' => 'h-8 w-8 text-caption-1', 'ring' => 'border-2',       'over' => '-space-x-2'],
        'md' => ['av' => 'h-10 w-10 text-caption-1', 'ring' => 'border-2',     'over' => '-space-x-1'],
    ];
    $s = $sizes[$size] ?? $sizes['xs'];

    $isCount = $overflow === 'count';
    $avatar = "relative inline-flex shrink-0 items-center justify-center rounded-full border-background-normal bg-deep-blue-800 font-bold text-white {$s['av']} {$s['ring']}";
    $counter = "relative inline-flex shrink-0 items-center justify-center rounded-full border-background-normal bg-primary font-medium text-white {$s['av']} {$s['ring']}";
@endphp

@if ($namesExpr)
    {{-- Alpine 모드 — 서버는 이름을 모른다. 자리만 만들고 Alpine 이 채운다. --}}
    <div {{ $attributes->class('inline-flex items-center gap-2') }}>
        <div class="flex items-center {{ $s['over'] }}">
            <template x-for="(n, i) in ({{ $namesExpr }}).slice(0, {{ $max }})" :key="i">
                <span class="{{ $avatar }}" x-text="String(n ?? '').trim().slice(0, 1)"></span>
            </template>
            @if ($isCount)
                <template x-if="({{ $namesExpr }}).length > {{ $max }}">
                    <span class="{{ $counter }}" x-text="'+' + (({{ $namesExpr }}).length - {{ $max }})"></span>
                </template>
            @endif
        </div>
        @if (! $isCount)
            <template x-if="({{ $namesExpr }}).length > {{ $max }}">
                <span class="whitespace-nowrap text-label-1 font-semibold text-label-alternative"
                      x-text="'외 ' + (({{ $namesExpr }}).length - {{ $max }}) + '명'"></span>
            </template>
        @endif
    </div>
@elseif (count($names))
    <div {{ $attributes->class('inline-flex items-center gap-2') }}>
        <div class="flex items-center {{ $s['over'] }}">
            @foreach ($shown as $n)
                <span class="{{ $avatar }}">{{ mb_substr($n, 0, 1) }}</span>
            @endforeach

            {{-- 원본은 초과 인원을 줄 밖 글자가 아니라 겹친 마지막 자리에 검정 원으로 낸다. --}}
            @if ($isCount && $extra > 0)
                <span class="{{ $counter }}">+{{ $extra }}</span>
            @endif
        </div>

        @if (! $isCount && $extra > 0)
            <span class="whitespace-nowrap text-label-1 font-semibold text-label-alternative">외 {{ $extra }}명</span>
        @endif
    </div>
@endif
