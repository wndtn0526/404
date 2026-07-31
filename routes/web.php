<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/health');

// 개발환경 스모크 테스트. 도메인 기능이 붙기 시작하면 지운다.
Route::livewire('/health', 'health-check')->name('health');
