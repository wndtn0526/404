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

// 포스팅 없는 경우 — Figma node 1104-59420. 같은 뷰에 빈 피드를 넘겨서 그 상태를 보여준다.
// 도메인이 붙으면 이 라우트는 지운다. 그때는 실제로 글이 없으면 저절로 이 화면이 된다.
Route::view('/public-space-empty', 'public-space', ['feed' => [], 'tab' => 'posting'])
    ->name('public-space.empty');

// 게시글 상세 — 퍼블릭 스페이스 피드 카드를 펼친 화면. Figma 는 모바일 노드(1104-59293)만 있다.
// 아직 정적이라 글 하나를 그대로 보여준다. 도메인이 붙으면 /post/{id} 로 바꾼다.
Route::view('/post', 'post')->name('post');

// 컨텐츠 관리 — Figma 워크스페이스 화면(node 1-299) 그대로.
Route::view('/contents', 'contents')->name('contents');
