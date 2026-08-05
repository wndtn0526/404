<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| 워크스페이스 내비게이션
|--------------------------------------------------------------------------
|
| <x-workspace-shell> 이 쓰는 내비게이션 정의. 화면마다 배열을 되풀이하지 않도록
| 한 곳에 모았다. 활성 상태(active)는 여기서 정하지 않는다 — 셸이 현재 경로와
| href 를 비교해 판정한다.
|
| href 는 앱 루트 기준 경로로 적는다. 셸이 url() 로 절대 URL 로 바꾼다.
| (정적 배포 스크립트가 절대 URL 만 상대 경로로 치환하기 때문이다.)
|
*/

return [

    /*
    | 좌측 레일 심볼 — 워크스페이스를 넘나드는 최상위 이동.
    |   icon : DS 아이콘(symbol). 활성 시 흰 면, 비활성 시 Neutral/40 면.
    |   mark : 브랜드 마크. 면 색은 그대로, 비활성 시 불투명도 30%.
    | 상태 표현은 Figma node 1-4661 이 정의한다.
    */
    'rail' => [
        ['icon' => 'compass', 'href' => '/community', 'label' => '커뮤니티', 'match' => ['community', 'community/*']],

        // 워크스페이스 심볼은 워크스페이스 안쪽 어디에 있든 켜져 있어야 한다.
        // href 한 곳만 보면 /contents 에서 꺼져 버린다.
        ['mark' => 'cdw-mark', 'href' => '/workspace', 'label' => '청담원 워크스페이스',
            'match' => ['workspace', 'workspace/*', 'contents', 'contents/*', 'settings', 'settings/*']],
    ],

    /*
    | 워크스페이스 안쪽 메뉴.
    */
    'items' => [
        ['label' => '홈', 'href' => '/workspace', 'icon' => 'home'],
        ['label' => '컨텐츠 관리', 'href' => '/contents', 'icon' => 'inbox'],
    ],

    // 설정 화면은 아직 없다. href 를 경로로 적으면 정적 배포에서 치환 대상이 없어
    // 절대 URL 검사가 빌드를 세운다. 화면이 생기면 '/settings' 로 바꾸고
    // export-styleguide.sh 의 PAGES 에도 추가한다.
    'footer_items' => [
        ['label' => '설정', 'href' => '#', 'icon' => 'setting'],
    ],

    /*
    | LNB 확대 배율. 1 = Figma 실측 그대로(240px).
    | 1.15 로 키워 봤다가 원래대로 되돌렸다. 넓은 모니터에서 비율이 작아 보이는 건
    | 240px 이 고정이라서인데, 원본을 벗어나는 쪽이 더 손해라고 판단했다.
    | 셸이 1 일 때는 zoom 속성 자체를 내보내지 않는다.
    */
    'lnb_scale' => 1.0,

];
