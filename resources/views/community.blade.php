{{-- 커뮤니티 — 레일의 나침반 심볼이 착지하는 곳.
     ⚠️ Figma 에 아직 이 화면 디자인이 없다. 레일 활성 상태를 실제로 확인할 대상이
        필요해서 셸만 두고 본문은 비워 뒀다. 디자인이 나오면 채운다.

     워크스페이스 밖이라 LNB 안쪽 메뉴는 넘기지 않는다 — 레일만 남는다. --}}
<x-layout title="커뮤니티">
    <x-workspace-shell
        workspace="커뮤니티"
        domain="cdw.workspace.io"
        user="김기안"
        has-alarm
        :rail="config('workspace.rail')"
        :scale="config('workspace.lnb_scale')"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => '커뮤니티']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">커뮤니티</h1>
        </x-slot:title>

        <p class="text-body-2 text-label-alternative">디자인 대기 중인 화면이다.</p>
    </x-workspace-shell>
</x-layout>
