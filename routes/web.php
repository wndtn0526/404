<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/styleguide');

// 개발환경 스모크 테스트. 도메인 기능이 붙기 시작하면 지운다.
Route::livewire('/health', 'health-check')->name('health');

// 살아있는 디자인 시스템 문서. 컴포넌트를 실제로 렌더해서 회귀를 눈으로 잡는다.
// 로컬 전용 — 운영에 올릴 땐 환경 가드(또는 인증 미들웨어)를 붙인다.
Route::view('/styleguide', 'styleguide')->name('styleguide');

// 워크스페이스 화면들. 아직 전부 정적이다.
// 레일 심볼의 착지점 — 나침반은 커뮤니티, 회사 심볼은 워크스페이스.
Route::view('/community', 'community')->name('community');
Route::view('/workspace', 'workspace')->name('workspace');

// 컨텐츠 관리 — Figma 워크스페이스 화면(node 1-299) 그대로.
Route::view('/contents', 'contents')->name('contents');
