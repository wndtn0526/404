{{-- 메뉴 바로가기 탭 — 화면 제목 옆에 붙는 알약 줄.
     Figma 원본(워크스페이스 Box34 · node 1-299) 모양 그대로이고, 동작이 다르다.

     ★ 이건 그 화면의 하위 탭이 아니라 「내가 들렀던 메뉴」가 쌓이는 바로가기다.
       브라우저 탭처럼 방문한 순서로 남고, 지금 보고 있는 메뉴가 검정 알약 + 닫기다.
       그래서 화면마다 목록을 박아 두면 안 된다 — 각 화면은 자기가 어느 메뉴인지만 알린다.

     쌓인 목록은 sessionStorage 에 둔다. 브라우저 탭을 닫으면 사라지는 게 맞다 —
     '이번에 들른 곳' 이지 영구 즐겨찾기가 아니다.

     props:
       menu  : 메뉴 식별자. 같은 메뉴의 하위 화면(예: 컨텐츠 추가)은 같은 값을 쓴다.
       label : 알약에 보일 이름
       href  : 그 메뉴의 대표 경로 (하위 화면에서도 목록 경로를 준다)
       max   : 최대 개수. 넘치면 오래된 것부터 버린다(지금 보는 건 안 버린다)

     ⚠️ 목록은 클라이언트에만 있으므로 서버가 그릴 수 없다. Alpine 이 붙기 전에는 지금
        메뉴 하나만 서버가 그려 두고, 붙는 순간 전체 목록으로 바꾼다. 그래야 처음에
        줄이 비었다가 튀지 않는다. JS 가 아예 없으면 그 하나가 그대로 남는다
        (display 유틸이 없어 inline 으로 그려진다 — 알약 모양은 그대로다).
     ⚠️ 닫기는 목록에서 빼기만 하고 화면은 그대로 둔다. 보고 있던 걸 닫아도 옮겨 가지 않는다
        — 누른 자리에서 사라지는 게 눌렀을 때 기대하는 동작이다.
        그 사이에는 목록에 없는 화면을 보고 있게 되는데, 새로 고치거나 다시 들르면 목록에
        다시 붙는다(들른 메뉴를 모으는 목록이라 그게 맞다). --}}
@props([
    'menu',
    'label',
    'href',
    'max' => 8,
])

@php
    /*
     * ⚠️ 알약에는 display 유틸(inline-flex)을 static class 로 두지 않는다.
     *    hidden 도 display 유틸이라 둘이 붙으면 CSS 순서로 inline-flex 가 이겨서,
     *    DOM 에 hidden 이 있는데도 화면에는 그대로 보인다. 그래서 display 자체를 토글한다.
     *    (표 행·트리 항목처럼 display 유틸이 없는 곳에서는 hidden 토글이 그냥 먹는다.)
     */
    $activePill = 'items-center justify-center gap-1 rounded-lg bg-mono-black pb-[5px] pl-3 pr-2.5 pt-1.5';
    $activeText = 'text-body-2 font-bold leading-[23px] text-white';
    $inactivePill = 'items-center justify-center rounded-lg bg-warm-gray-200 px-3 pb-[5px] pt-1.5 text-body-2 font-bold leading-[23px] text-warm-gray-500 transition-colors hover:text-label-normal';
@endphp

<div {{ $attributes->class('flex flex-wrap items-start gap-4') }}
     x-data="dsMenuTabs(@js($menu), @js($label), @js(url($href)), {{ (int) $max }})">

    {{-- Alpine 이 붙기 전 · JS 가 없을 때 — 지금 메뉴만 --}}
    <span class="{{ $activePill }}" x-bind:class="ready ? 'hidden' : 'inline-flex'">
        <span class="{{ $activeText }}">{{ $label }}</span>
    </span>

    <template x-for="tab in tabs" :key="tab.menu">
        <span class="contents">
            {{-- 지금 보고 있는 메뉴 --}}
            <span class="{{ $activePill }}" x-bind:class="ready && tab.menu === current ? 'inline-flex' : 'hidden'">
                <span class="{{ $activeText }}" x-text="tab.label"></span>
                <button type="button" @click="close(tab.menu)"
                        class="inline-flex shrink-0 items-center pb-px text-white transition-opacity hover:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/40"
                        x-bind:aria-label="tab.label + ' 바로가기 닫기'">
                    <x-icon-close class="size-[18px]" />
                </button>
            </span>

            {{-- 들렀던 다른 메뉴 --}}
            <a x-bind:href="tab.href" class="{{ $inactivePill }}"
               x-bind:class="ready && tab.menu !== current ? 'inline-flex' : 'hidden'" x-text="tab.label"></a>
        </span>
    </template>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('dsMenuTabs', (menu, label, href, max) => ({
                    tabs: [],
                    current: menu,
                    ready: false,

                    init() {
                        const list = this.load();

                        // 이번 화면의 메뉴가 목록에 없으면 맨 뒤에 붙는다(방문 순서).
                        if (! list.some((t) => t.menu === menu)) {
                            list.push({ menu, label, href });
                        }

                        // 넘치면 오래된 것부터 버린다. 지금 보고 있는 건 남긴다.
                        while (list.length > max) {
                            const i = list.findIndex((t) => t.menu !== menu);
                            if (i === -1) break;
                            list.splice(i, 1);
                        }

                        this.tabs = list;
                        this.save();
                        this.ready = true;
                    },

                    load() {
                        // 저장된 값이 깨져 있어도 화면이 죽지 않게 한다.
                        try {
                            const raw = JSON.parse(sessionStorage.getItem('cdw.menuTabs') || '[]');
                            return Array.isArray(raw)
                                ? raw.filter((t) => t && t.menu && t.label && t.href)
                                : [];
                        } catch (e) {
                            return [];
                        }
                    },

                    save() {
                        try {
                            sessionStorage.setItem('cdw.menuTabs', JSON.stringify(this.tabs));
                        } catch (e) {
                            // 시크릿 모드 등에서 저장이 막혀도 이번 화면은 그대로 쓴다.
                        }
                    },

                    // 목록에서 빼기만 한다. 보고 있던 걸 닫아도 화면은 그대로 둔다 —
                    // 누른 자리에서 사라지는 게 눌렀을 때 기대하는 동작이다.
                    close(target) {
                        const i = this.tabs.findIndex((t) => t.menu === target);
                        if (i === -1) return;

                        this.tabs.splice(i, 1);
                        this.save();
                    },
                }));
            });
        </script>
    @endpush
@endonce
