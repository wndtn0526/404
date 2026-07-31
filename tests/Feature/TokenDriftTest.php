<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * 이 저장소의 tokens.css 는 청담원 플랫폼 tailwind.config.js 를 Tailwind 4 문법으로
 * 옮긴 것이다. 두 파일이 몰래 갈라지면 같은 이름의 토큰이 다른 색을 뜻하게 되고,
 * 그때부터 "DS 를 쓴다"는 말이 거짓이 된다.
 *
 * 청담원 플랫폼 저장소가 로컬에 있을 때만 돈다:
 *   CDW_PLATFORM_PATH=~/cheongdamwon-platform vendor/bin/sail artisan test
 * 경로가 없으면 조용히 skip 한다 (CI·다른 사람 머신에서 깨지지 않게).
 */
class TokenDriftTest extends TestCase
{
    /** 이름이 다르게 붙은 토큰 매핑: 청담원 config 키 → 이 저장소 CSS 변수 */
    private const CHECKS = [
        '#3694ab' => '--color-primary',
        '#04718b' => '--color-primary-strong',
        '#054e60' => '--color-primary-heavy',
        '#e8f3f5' => '--color-primary-surface',
        '#f1f8f9' => '--color-primary-soft',
        '#b8dce4' => '--color-inverse-primary',
        '#171719' => '--color-label-normal',
        '#f7f7f8' => '--color-background-alternative',
        '#e1e2e4' => '--color-line-solid-normal',
        '#00bf40' => '--color-status-positive',
        '#ff9200' => '--color-status-cautionary',
        '#ff4242' => '--color-status-negative',
        '#e52222' => '--color-accent-fg-red',
        '#005eeb' => '--color-accent-fg-blue',
        '#989ba2' => '--color-interaction-inactive',
    ];

    private function tokensCss(): string
    {
        return file_get_contents(resource_path('css/tokens.css'));
    }

    /** 우리 쪽 토큰이 기대한 DS 값을 들고 있는지. 플랫폼 저장소 없이도 돈다. */
    public function test_tokens_hold_the_expected_ds_values(): void
    {
        $css = strtolower($this->tokensCss());

        foreach (self::CHECKS as $hex => $var) {
            $this->assertMatchesRegularExpression(
                '/'.preg_quote($var, '/').':\s*'.preg_quote($hex, '/').'\b/',
                $css,
                "{$var} 가 DS 값 {$hex} 이 아니다."
            );
        }
    }

    /** 타이포 스케일 16단계가 size·line-height·letter-spacing 세 값을 모두 갖는지. */
    public function test_type_scale_is_complete(): void
    {
        $css = $this->tokensCss();

        $steps = [
            'display-1', 'display-2', 'display-3',
            'title-1', 'title-2', 'title-3',
            'heading-1', 'heading-2',
            'headline-1', 'headline-2',
            'body-1', 'body-2',
            'label-1', 'label-2',
            'caption-1', 'caption-2',
        ];

        foreach ($steps as $step) {
            foreach (['', '--line-height', '--letter-spacing'] as $suffix) {
                $this->assertStringContainsString(
                    "--text-{$step}{$suffix}:",
                    $css,
                    "타이포 토큰 --text-{$step}{$suffix} 가 없다."
                );
            }
        }
    }

    /** 청담원 플랫폼이 로컬에 있으면, 그쪽 원본 값과 한 글자씩 대조한다. */
    public function test_no_drift_from_the_cheongdamwon_platform(): void
    {
        $path = getenv('CDW_PLATFORM_PATH') ?: null;

        if (! $path) {
            $this->markTestSkipped('CDW_PLATFORM_PATH 미설정 — 청담원 플랫폼과의 대조를 건너뜀.');
        }

        $config = rtrim($path, '/').'/tailwind.config.js';

        if (! is_readable($config)) {
            $this->markTestSkipped("청담원 플랫폼 tailwind.config.js 를 읽을 수 없다: {$config}");
        }

        $source = strtolower(file_get_contents($config));
        $ours = strtolower($this->tokensCss());
        $drifted = [];

        foreach (self::CHECKS as $hex => $var) {
            $inSource = str_contains($source, $hex);
            $inOurs = (bool) preg_match('/'.preg_quote($var, '/').':\s*'.preg_quote($hex, '/').'\b/', $ours);

            if ($inSource !== $inOurs) {
                $drifted[$var] = $inSource
                    ? "청담원엔 {$hex} 가 있는데 우리 토큰엔 없다"
                    : "우리 토큰의 {$hex} 가 청담원엔 없다 (청담원이 바뀌었나?)";
            }
        }

        $this->assertSame([], $drifted, '청담원 DS 와 토큰이 갈라졌다: '.json_encode($drifted, JSON_UNESCAPED_UNICODE));
    }
}
