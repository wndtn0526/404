<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/styleguide');

// 개발환경 스모크 테스트. 도메인 기능이 붙기 시작하면 지운다.
Route::livewire('/health', 'health-check')->name('health');

// 살아있는 디자인 시스템 문서. 컴포넌트를 실제로 렌더해서 회귀를 눈으로 잡는다.
// 로컬 전용 — 운영에 올릴 땐 환경 가드(또는 인증 미들웨어)를 붙인다.
Route::view('/styleguide', 'styleguide')->name('styleguide');

// 워크스페이스 화면들. 아직 전부 정적이다.
// 레일 심볼의 착지점 — 나침반은 퍼블릭 스페이스, 회사 심볼은 워크스페이스.
Route::view('/public-space', 'public-space')->name('public-space');
Route::view('/workspace', 'workspace')->name('workspace');

// 프로필 설정 — Figma node 1104-58578. 퍼블릭 스페이스의 '프로필 설정' 버튼이 오는 곳.
Route::view('/profile-settings', 'profile-settings')->name('profile-settings');

// 포스팅 없는 경우 — Figma node 1104-59420. 같은 뷰에 빈 피드를 넘겨서 그 상태를 보여준다.
// 도메인이 붙으면 이 라우트는 지운다. 그때는 실제로 글이 없으면 저절로 이 화면이 된다.
Route::view('/public-space-empty', 'public-space', ['feed' => [], 'tab' => 'posting'])
    ->name('public-space.empty');

// 게시글 상세 — 퍼블릭 스페이스 피드 카드를 펼친 화면. Figma 는 모바일 노드(1104-59293)만 있다.
// 아직 정적이라 글 하나를 그대로 보여준다. 도메인이 붙으면 /post/{id} 로 바꾼다.
Route::view('/post', 'post')->name('post');

// 컨텐츠 관리 — Figma 워크스페이스 화면(node 1-299) 그대로.
Route::view('/contents', 'contents')->name('contents');

// 컨텐츠 추가 — 레이아웃은 Figma node 1002-269747, 필드는 컨텐츠 관리 표의 컬럼.
// 아직 정적이다. 저장이 붙으면 POST 라우트와 컨트롤러를 따로 만든다.
Route::view('/contents/new', 'contents-create')->name('contents.create');

// 컨텐츠 상세 — 표에서 행을 누르면 온다. 필드 구조는 Figma node 1002-275959 참고.
// 정적이라 컨텐츠 하나만 보여준다. 모델이 붙으면 /contents/{id} 로 바꾼다.
Route::view('/contents/detail', 'contents-detail')->name('contents.detail');

// 과정 관리 — 컨텐츠를 묶어 만든 '과정'. Figma 에 디자인이 없어 컨텐츠 관리와 같은 뼈대로 짰다.
Route::view('/courses', 'courses')->name('courses');
Route::view('/courses/new', 'courses-create')->name('courses.create');

// 화상조직도 — 회사 아래로 조직을 잇고 조직장·멤버를 담는다. Figma node 1002-279525.
Route::view('/organization', 'organization')->name('organization');

// 조직 관리 — 좌측 조직도 트리 + 우측 조직 상세. Figma node 1002-274184.
Route::view('/orgs', 'orgs')->name('orgs');

// 변경 이력 수정 — 조직 관리 > 변경 이력 탭의 '변경 이력 추가' 가 오는 곳.
// 입력할 값이 많아서 팝업이 아니라 페이지다. Figma node 1002-274589.
Route::view('/orgs/history', 'orgs-history-edit')->name('orgs.history');

// 과정 상세 — 표에서 행을 누르면 온다. 필드 구조는 컨텐츠 상세와 같다.
// 정적이라 과정 하나만 보여준다. 모델이 붙으면 /courses/{id} 로 바꾼다.
Route::view('/courses/detail', 'courses-detail')->name('courses.detail');

// 재무 — 지출 현황 대시보드 (Figma node 1002-88730). 값은 전부 뷰에 박아둔 예시다.
Route::view('/finance', 'finance')->name('finance');

// 재무 > 업무 관리자 메뉴 > 예산 계정 관리 (Figma node 1002-93118).
// 같은 탭 줄의 나머지 탭은 아직 화면이 없다.
Route::view('/finance/budget', 'budget-accounts')->name('finance.budget');

// 재무 > 업무 관리자 메뉴 > 개인 비용 정산 (1002-92654) · 지출 결의서 정산 (1002-92909)
Route::view('/finance/personal', 'personal-settlement')->name('finance.personal');
Route::view('/finance/expense', 'expense-settlement')->name('finance.expense');

// 전자결재 — 문서 신청 (Figma node 1002-106228). 이 저장소의 본 도메인 첫 화면이다.
Route::view('/documents', 'documents')->name('documents');

// 전자결재 — 확인할 문서 (Figma node 1002-106148). 나에게 돌아온 결재 받은함이다.
Route::view('/documents/review', 'documents-review')->name('documents.review');

// 전자결재 — 기안 작성 (Figma node 1002-113826). 지금은 '지출 결의서 (개인 비용)' 하나뿐이라
// 양식을 경로로 받지 않는다. 양식이 늘면 /documents/new/{form} 으로 나눈다.
Route::view('/documents/new', 'documents-create')->name('documents.create');

// 확인할 문서 — 문서가 없을 때 (Figma node 1002-106604).
// 퍼블릭 스페이스 빈 화면과 같은 방식으로, 같은 뷰에 빈 배열을 넘겨 상태만 바꾼다.
Route::view('/documents/review-empty', 'documents-review', ['notices' => []])
    ->name('documents.review.empty');

/*
 * 기안 작성 — 다 채운 화면 (Figma node 1002-115013).
 * 빈 화면과 같은 뷰다. 팝업으로 하나씩 채워 넣어도 이 모양이 된다.
 *
 * ⚠️ 계좌 번호는 원본 값을 그대로 쓰지 않았다. 원본은 실제 형식의 번호라
 *    저장소·정적 배포본에 남기지 않는 게 맞다(CLAUDE.md · 조직 지침).
 *    자리수만 맞춘 0 이다. 은행 이름도 원본 그대로 가상의 'GPRO 뱅크' 다.
 * ⚠️ 값은 전부 예시다. DB 에서 오지 않는다.
 */
Route::view('/documents/new-done', 'documents-create', [
    'prefill' => [
        'form' => [
            'name' => '지출 결의서 (개인 비용)',
            'month' => '2021.10',
            'bank' => 'GPRO 뱅크',
            'account' => '0000 00 0000000',
        ],
        'details' => [[
            'project' => '회사 운영',
            'category' => '기타 수수료',
            'account' => '기타 수수료',
            'used_at' => '2021.12.30',
            'memo' => '내용 없음',
            'vendor' => 'GPRO',
            'amount' => '3,000,000 원',
        ]],
        'files' => [
            ['name' => 'Receipt_20210801.jpg', 'ext' => 'JPG'],
        ],
        'refs' => [
            ['no' => 'CDW-210801-003', 'kind' => '업무', 'used_at' => '2021.08.01 03:30',
                'title' => '지출 결의서 · 기타 수수료', 'writer' => '김기안'],
        ],
        // 신청(본인)은 화면이 늘 맨 위에 그린다. 여기엔 그 아래 줄만 담는다.
        'line' => [
            ['name' => '정프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'progress'],
            ['name' => '곽프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'view'],
            ['name' => '황프로', 'dept' => 'GPRO 그룹 · COO', 'role' => 'ref'],
        ],
    ],
])->name('documents.create.done');

/*
 * 기안 작성 — 결재선에 사람이 많을 때 (Figma node 1002-115437).
 * 열람·참조는 갈래마다 4명까지만 줄로 늘어놓고 넘으면 아바타 한 줄로 접는다.
 * 접힌 줄을 누르면 이름 목록이 뜬다(node 1002-115544).
 * 진행(승인)은 순서가 뜻을 가지므로 접지 않는다 — 원본도 다섯이면 다섯 줄이다(node 1002-115532).
 *
 * 위의 다 채운 화면과 같은 뷰다. 결재선만 바꿔 넘긴다.
 */
Route::view('/documents/new-crowd', 'documents-create', [
    'prefill' => [
        'form' => [
            'name' => '지출 결의서 (개인 비용)',
            'month' => '2021.10',
            'bank' => 'GPRO 뱅크',
            'account' => '0000 00 0000000',
        ],
        'details' => [[
            'project' => '회사 운영',
            'category' => '기타 수수료',
            'account' => '기타 수수료',
            'used_at' => '2021.12.30',
            'memo' => '내용 없음',
            'vendor' => 'GPRO',
            'amount' => '3,000,000 원',
        ]],
        'files' => [['name' => 'Receipt_20210801.jpg', 'ext' => 'JPG']],
        'refs' => [],
        'line' => [
            ['name' => '정프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'progress'],
            ['name' => '오프로', 'dept' => 'GPRO 그룹 · 개발', 'role' => 'view'],
            ['name' => '최프로', 'dept' => 'GPRO 그룹 · 개발', 'role' => 'view'],
            ['name' => '장프로', 'dept' => 'GPRO 그룹 · 운영', 'role' => 'view'],
            ['name' => '곽프로', 'dept' => 'GPRO 그룹 · 운영', 'role' => 'view'],
            ['name' => '황프로', 'dept' => 'GPRO 그룹 · COO', 'role' => 'view'],
            ['name' => '문프로', 'dept' => 'GPRO 그룹 · 콘텐츠', 'role' => 'view'],
            ['name' => '유프로', 'dept' => 'GPRO 그룹 · 콘텐츠', 'role' => 'ref'],
            ['name' => '한프로', 'dept' => 'GPRO 그룹 · 마케팅', 'role' => 'ref'],
            ['name' => '조프로', 'dept' => 'GPRO 그룹 · 마케팅', 'role' => 'ref'],
            ['name' => '배프로', 'dept' => 'GPRO 그룹 · 디자인', 'role' => 'ref'],
            ['name' => '신프로', 'dept' => 'GPRO 그룹 · 디자인', 'role' => 'ref'],
        ],
    ],
])->name('documents.create.crowd');

/*
 * 휴가 신청 (Figma node 1002-108468).
 * 기안 작성과 같은 뷰다 — 왼쪽 첫 카드만 partials/doc-form-vacation 으로 바뀐다.
 * 종류를 고르면 '사용할 날짜' 가, 시간을 받는 종류면 '사용할 시간' 까지 생긴다.
 */
Route::view('/documents/vacation', 'documents-create', [
    'docForm' => 'vacation',
    'docTitle' => '휴가 신청',
])->name('documents.vacation');

// 휴가 신청 — 다 채운 화면 (Figma node 1002-108556). 값은 전부 예시다.
Route::view('/documents/vacation-done', 'documents-create', [
    'docForm' => 'vacation',
    'docTitle' => '휴가 신청',
    'prefill' => [
        'form' => [
            'name' => '휴가 신청',
            'leave_type' => '연차',
            'leave_date' => '2021.10.01 - 2021.10.01',
            'reason' => '휴가를 신청합니다.',
        ],
        'leave' => ['total' => 15, 'used' => 8],
        'files' => [['name' => 'Receipt_20210801.jpg', 'ext' => 'JPG']],
        'refs' => [
            ['no' => 'CDW-210801-001', 'kind' => '업무', 'used_at' => '2021.08.01 03:30',
                'title' => '휴가 관련 문서', 'writer' => '김기안'],
        ],
        'line' => [
            ['name' => '정프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'progress'],
            ['name' => '곽프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'view'],
            ['name' => '황프로', 'dept' => 'GPRO 그룹 · COO', 'role' => 'ref'],
        ],
    ],
])->name('documents.vacation.done');

/*
 * 지출 결의서 (거래처) — Figma node 1002-108416(빈) · 1002-108333(다 채운).
 * 개인 비용과 뼈대는 같고 귀속처·예금주·파트너사·사업자 번호가 늘고,
 * 상세 내용 표에서 거래처 열이 빠진다. 카드 아래에 긴급 문서 토글이 붙는다.
 */
Route::view('/documents/vendor', 'documents-create', [
    'docForm' => 'vendor',
    'docTitle' => '지출 결의서 (거래처)',
])->name('documents.vendor');

/*
 * 지출 결의서 (거래처) — 다 채운 화면.
 * ⚠️ 계좌 번호·사업자 번호는 원본 값을 그대로 쓰지 않았다. 실제 형식의 번호를 저장소·정적
 *    배포본에 남기지 않는 게 맞다(CLAUDE.md · 조직 지침). 자리수만 맞춘 0 이다.
 * ⚠️ 값은 전부 예시다.
 */
Route::view('/documents/vendor-done', 'documents-create', [
    'docForm' => 'vendor',
    'docTitle' => '지출 결의서 (거래처)',
    'prefill' => [
        'form' => [
            'name' => '지출 결의서 · 거래처',
            'org' => '뉴 게임즈',
            'month' => '2021.08',
            'bank' => 'GPRO 뱅크',
            'account' => '0000 00 0000000',
            'holder' => '심프로',
            'partner' => '네이버',
            'biz_no' => '000-00-00000',
            'urgent' => true,
            'pay_due' => '2021.08.30',
        ],
        'details' => [[
            'project' => '디자인 작업',
            'category' => '업무 비용',
            'account' => '프로그램 구입',
            'used_at' => '2021.12.30',
            'memo' => '프로그램 구입',
            'vendor' => '',
            'amount' => '3,000,000 원',
        ]],
        'files' => [['name' => 'Receipt_20210801.jpg', 'ext' => 'JPG']],
        'refs' => [
            ['no' => 'CDW-210801-003', 'kind' => '업무', 'used_at' => '2021.08.01 03:30',
                'title' => '지출 결의서 · 기타 수수료', 'writer' => '김기안'],
        ],
        'line' => [
            ['name' => '정프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'progress'],
            ['name' => '곽프로', 'dept' => 'GPRO 그룹 · CFO', 'role' => 'view'],
            ['name' => '황프로', 'dept' => 'GPRO 그룹 · COO', 'role' => 'ref'],
        ],
    ],
])->name('documents.vendor.done');
