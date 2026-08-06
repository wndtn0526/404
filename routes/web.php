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

// 과정 상세 — 표에서 행을 누르면 온다. 필드 구조는 컨텐츠 상세와 같다.
// 정적이라 과정 하나만 보여준다. 모델이 붙으면 /courses/{id} 로 바꾼다.
Route::view('/courses/detail', 'courses-detail')->name('courses.detail');
