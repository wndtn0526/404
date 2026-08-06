<?php

namespace Tests\Feature;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class ContentsCreateTest extends TestCase
{
    public function test_create_form_renders(): void
    {
        $this->get('/contents/new')
            ->assertOk()
            ->assertSee('기본 설정 (필수)')
            ->assertSee('상세 설정');
    }

    /**
     * 등록일 칸은 오늘 날짜를 그대로 보여준다.
     *
     * '저장하면 자동 기록' 같은 문구로 되돌아가면 이 테스트가 잡는다. 표기는 컨텐츠 관리
     * 표와 같은 Y.m.d 다 — 표와 폼이 다른 형식을 쓰면 같은 값인지 눈으로 알 수 없다.
     */
    public function test_registration_date_field_shows_today(): void
    {
        $this->get('/contents/new')
            ->assertOk()
            ->assertSee(Carbon::now()->format('Y.m.d'));
    }

    /**
     * 앱 타임존은 .env(APP_TIMEZONE)에서 와야 한다.
     *
     * config/app.php 에 'UTC' 가 하드코딩돼 있어서 .env 의 Asia/Seoul 이 무시되고 있었다.
     * UTC 는 KST 보다 9시간 느리므로 KST 00~09시 사이에는 날짜가 하루 전으로 찍힌다.
     * 등록일·결재 기한처럼 날짜로 판단하는 값이 전부 하루씩 틀어지는 종류의 버그다.
     *
     * config 값 자체를 단정하지 않는 이유: .env 는 저장소에 없고 테스트 환경마다 다르다.
     * 하드코딩으로 되돌아갔는지만 원본 파일에서 확인한다.
     */
    public function test_app_timezone_is_read_from_env(): void
    {
        $config = file_get_contents(config_path('app.php'));

        $this->assertMatchesRegularExpression(
            "/'timezone'\s*=>\s*env\('APP_TIMEZONE'/",
            $config,
            "config/app.php 의 timezone 이 env('APP_TIMEZONE', ...) 를 읽지 않는다. ".
            '값을 박아 두면 .env 의 Asia/Seoul 이 무시돼 KST 00~09시에 날짜가 하루 전으로 찍힌다.'
        );
    }
}
