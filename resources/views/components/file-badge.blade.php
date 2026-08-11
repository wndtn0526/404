{{-- 파일 종류 배지 — Figma GPRO_PORTFOLIO `jpg_24` (node I1002:115045;3302:167197) ·
     `doc_24` (node I1002:115030;3302:167455). 모서리를 접은 문서 모양에 확장자를 얹는다.

     원본 실측 — 바깥 24x24 · 안쪽 글리프 19.4107x24 (좌우 9.56% 안쪽, 가로 가운데)
                 확장자 글자 5 Bold · 가운데 정렬 · 글리프 위에서 9.6

     path 는 Figma 에서 내려받은 것을 그대로 옮겼다. 색만 원시 토큰으로 바꿨다 —
     원본 두 벌은 모양이 같고 색만 다르다(JPG deep blue · DOC purple).

     ⚠️ DS 아이콘 219종에는 이 글리프가 없다. `document` 는 단색 외곽선이라 확장자를
        얹을 자리가 없다. 그래서 DS 밖 컴포넌트로 뒀다 — svg/ext 가 아닌 이유는 두 가지
        색과 글자를 받아야 해서다(blade-icons 는 글자를 넣지 못한다).
     ⚠️ 원본이 정하는 색은 둘뿐이다. 그 밖의 확장자는 문서(purple)로 떨어진다.
        디자이너가 색을 더 정하면 $tones 에 추가한다.

     props
       label : 배지에 찍을 글자. 3글자까지 들어간다 (JPG · DOC · PDF …).
       tone  : deep-blue(이미지) | purple(문서) | auto(label 로 고른다)
       labelExpr : Alpine 식. 주면 label 대신 이 식의 값이 찍힌다 (x-for 안에서 쓴다). --}}
@props([
    'label' => '',
    'tone' => 'auto',
    'labelExpr' => null,
])

@php
    // ⚠️ Tailwind 는 파일을 문자열로 훑는다. 완성된 클래스명을 담는다.
    $tones = [
        'deep-blue' => ['body' => 'fill-deep-blue-800', 'fold' => 'fill-deep-blue-900', 'text' => 'fill-deep-blue-300'],
        'purple' => ['body' => 'fill-purple-800', 'fold' => 'fill-purple-900', 'text' => 'fill-purple-300'],
    ];

    // 원본은 사진(JPG)을 deep blue, 문서(DOC)를 purple 로 쓴다. 그 갈래만 따른다.
    $images = ['JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'HEIC', 'SVG', 'BMP', 'TIF', 'TIFF'];
    $picked = $tone === 'auto'
        ? (in_array(strtoupper($label), $images, true) ? 'deep-blue' : 'purple')
        : $tone;
    $c = $tones[$picked] ?? $tones['purple'];
@endphp

<svg {{ $attributes->class('size-6 shrink-0') }} viewBox="0 0 24 24" fill="none"
     xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    {{-- 내보낸 글리프는 19.4107 폭이다. 24 안에서 가로 가운데로 밀어 원본 여백을 지킨다. --}}
    <g transform="translate(2.2946 0)">
        <path class="{{ $c['body'] }}"
              d="M12.1317 0H2.42633C1.78283 0 1.16568 0.252856 0.710656 0.702944C0.25563 1.15303 0 1.76348 0 2.4V21.6C0 22.2365 0.25563 22.847 0.710656 23.2971C1.16568 23.7471 1.78283 24 2.42633 24H16.9843C17.6278 24 18.245 23.7471 18.7 23.2971C19.155 22.847 19.4106 22.2365 19.4106 21.6V7.44828L12.1317 0Z" />
        <path class="{{ $c['fold'] }}"
              d="M12.1317 0V6.44831C12.1317 7.00059 12.5794 7.44831 13.1317 7.44831H19.4107" />
    </g>
    <text x="12" y="13.6" text-anchor="middle" font-size="5" font-weight="700"
          class="{{ $c['text'] }}"
          @if ($labelExpr) x-text="{{ $labelExpr }}" @endif>{{ $label }}</text>
</svg>
