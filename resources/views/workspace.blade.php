{{-- 워크스페이스 홈 — 레일의 회사 심볼이 착지하는 곳.
     ⚠️ Figma 에 아직 이 화면 디자인이 없다. 레일 활성 상태를 실제로 확인할 대상이
        필요해서 셸만 두고 본문은 비워 뒀다. 디자인이 나오면 채운다. --}}
<x-layout title="워크스페이스">
    <x-workspace-shell
        workspace="청담원"
        domain="cdw.workspace.io"
        user="김기안"
        has-alarm
        :rail="config('workspace.rail')"
        :items="config('workspace.items')"
        :footer-items="config('workspace.footer_items')"
        :scale="config('workspace.lnb_scale')"
    >
        <x-slot:breadcrumb>
            <x-breadcrumb :items="[['label' => '홈']]" />
        </x-slot:breadcrumb>

        <x-slot:title>
            <h1 class="text-title-2 font-bold text-mono-black">홈</h1>
            <x-menu-tabs menu="home" label="홈" href="/workspace" />
        </x-slot:title>

        <p class="text-body-2 text-label-alternative">디자인 대기 중인 화면이다.</p>
    </x-workspace-shell>
</x-layout>
