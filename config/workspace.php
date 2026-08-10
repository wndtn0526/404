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
        ['icon' => 'compass', 'href' => '/public-space', 'label' => '퍼블릭 스페이스',
            'match' => ['public-space', 'public-space/*']],

        // 워크스페이스 심볼은 워크스페이스 안쪽 어디에 있든 켜져 있어야 한다.
        // href 한 곳만 보면 /contents 에서 꺼져 버린다.
        ['mark' => 'cdw-mark', 'href' => '/workspace', 'label' => '청담원 워크스페이스',
            'match' => ['workspace', 'workspace/*', 'contents', 'contents/*',
                'courses', 'courses/*', 'organization', 'organization/*', 'orgs', 'orgs/*',
                'documents', 'documents/*',
                'finance', 'finance/*', 'settings', 'settings/*']],
    ],

    /*
    | 퍼블릭 스페이스 안쪽 메뉴 — 워크스페이스와 별개다 (Figma node 1104-55195).
    */
    // 아티클·그룹 화면은 아직 없다. 경로로 적으면 정적 배포에서 치환 대상이 없어
    // 절대 URL 검사가 빌드를 세운다. 화면이 생기면 경로로 바꾸고 PAGES 에도 추가한다.
    'public_items' => [
        ['label' => '홈', 'href' => '/public-space', 'icon' => 'home'],
        ['label' => '아티클', 'href' => '#', 'icon' => 'document'],
        ['label' => '그룹', 'href' => '#', 'icon' => 'persons'],
    ],

    /*
    | 워크스페이스 안쪽 메뉴.
    */
    // 컨텐츠 관리는 하위 화면(/contents/new 등)에서도 켜져 있어야 한다.
    // href 한 곳만 보면 등록 화면에서 메뉴가 꺼진다.
    //
    // 과정 관리도 마찬가지다. 목록(/courses)과 추가(/courses/new) 두 화면을 쓴다.
    /*
    | 워크스페이스 안쪽 메뉴.
    |
    | 원본 LNB 는 워크스페이스·재무 밑에 한 단을 더 둔다(전자결재 node 1002-106228 ·
    | 재무 node 1002-93118). 화면이 늘면서 평평하게 열 개가 늘어서 있었는데 원본대로 접었다.
    | 하위 항목은 아이콘 없이 글자만 나가고, 자식이 켜지면 부모도 같이 켜진다.
    |
    | match 는 하위 화면까지 포함한다 — /contents/new 에서 '컨텐츠 관리' 가 꺼지면 안 된다.
    */
    'items' => [
        ['label' => '홈', 'href' => '/workspace', 'icon' => 'home',
            'match' => ['workspace']],

        // 전자결재. 원본은 여기에 내 문서함·문서 관리자 메뉴·시스템 관리자 메뉴가
        // 더 있다 — 화면이 생기면 같이 넣는다.
        ['label' => '워크스페이스', 'href' => '/documents', 'icon' => 'document-text',
            'match' => ['documents', 'documents/*'], 'children' => [
                ['label' => '문서 신청', 'href' => '/documents', 'match' => ['documents']],
                ['label' => '확인할 문서', 'href' => '/documents/review', 'match' => ['documents/review']],
            ]],

        // ⚠️ 컨텐츠 관리·과정 관리는 묶지 않는다. 둘을 감쌀 이름이 원본에 없어서 부모를
        //    '컨텐츠 관리' 로 두면 같은 이름이 두 번 나온다.
        ['label' => '컨텐츠 관리', 'href' => '/contents', 'icon' => 'inbox',
            'match' => ['contents', 'contents/*']],
        ['label' => '과정 관리', 'href' => '/courses', 'icon' => 'graduation',
            'match' => ['courses', 'courses/*']],

        ['label' => '화상조직도', 'href' => '/organization', 'icon' => 'company',
            'match' => ['organization', 'organization/*']],
        ['label' => '조직 관리', 'href' => '/orgs', 'icon' => 'persons',
            'match' => ['orgs', 'orgs/*']],

        ['label' => '재무', 'href' => '/finance', 'icon' => 'coins',
            'match' => ['finance', 'finance/*'], 'children' => [
                ['label' => '지출 현황 대시보드', 'href' => '/finance', 'match' => ['finance']],
                ['label' => '개인 비용 정산', 'href' => '/finance/personal', 'match' => ['finance/personal']],
                ['label' => '지출 결의서 정산', 'href' => '/finance/expense', 'match' => ['finance/expense']],
                ['label' => '예산 계정 관리', 'href' => '/finance/budget', 'match' => ['finance/budget']],
            ]],
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
