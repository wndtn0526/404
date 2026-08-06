{{-- 워크스페이스 알약 탭 — Figma 워크스페이스 화면 원본 Box34 (node 1-299).
     활성은 검정 채움 + 닫기 버튼, 비활성은 Warm gray/200 면에 Warm gray/500 글자.
     DS <x-tabs> 는 밑줄형이라 형태가 다르다. 원본이 알약형이어서 여기서 조립했다.

     컨텐츠 관리 · 과정 관리 · 조직 관리가 같은 탭 줄을 쓴다. 화면마다 되풀이하지 않으려고 뺐다.

     $active = 'contents' | 'courses' | 'orgs' --}}
@php
    $tabs = [
        'contents' => ['label' => '컨텐츠 관리', 'href' => url('/contents')],
        'courses' => ['label' => '과정 관리', 'href' => url('/courses')],
        'orgs' => ['label' => '조직 관리', 'href' => url('/orgs')],
    ];
@endphp

<div class="flex flex-wrap items-start gap-4">
    @foreach ($tabs as $key => $tab)
        @if ($key === $active)
            <span class="inline-flex items-center justify-center gap-1 rounded-lg bg-mono-black pb-[5px] pl-3 pr-2.5 pt-1.5">
                <span class="text-body-2 font-bold leading-[23px] text-white">{{ $tab['label'] }}</span>
                {{-- 닫기는 원본에 있는 모양이다. 탭을 실제로 닫는 동작은 아직 없다. --}}
                <button type="button"
                        class="inline-flex shrink-0 items-center pb-px text-white transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                        aria-label="{{ $tab['label'] }} 탭 닫기">
                    <x-icon-close class="size-[18px]" />
                </button>
            </span>
        @else
            <a href="{{ $tab['href'] }}"
               class="inline-flex items-center justify-center rounded-lg bg-warm-gray-200 px-3 pb-[5px] pt-1.5 text-body-2 font-bold leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal">
                {{ $tab['label'] }}
            </a>
        @endif
    @endforeach
</div>
