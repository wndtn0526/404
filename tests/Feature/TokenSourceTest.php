<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * tokens.css 는 Figma "GPRO_PORTFOLIO" Design guide(node 1002-517500)에서 뽑은 값이다.
 * 원본과 몰래 갈라지면 같은 이름의 토큰이 다른 색을 뜻하게 되고,
 * 그때부터 "DS 를 쓴다"는 말이 거짓이 된다.
 *
 * resources/design/gpro-tokens.json 이 Figma 추출본(Dev Mode MCP `get_variable_defs`)이다.
 * Figma 가 바뀌면 이 JSON 을 다시 뽑아 넣고, 이 테스트가 빨개지는 토큰을 손으로 맞춘다.
 */
class TokenSourceTest extends TestCase
{
    /** 의미 계층에 채택하지 않은 GPRO 변수 — 값이 아니라 "왜 뺐는지"를 고정한다. */
    private const DELIBERATELY_UNUSED = [
        '#3c3c43' => 'Label Color / Separator Colors — iOS 잔재. Cool gray 로 대체했다.',
        '#007aff' => 'SystemBlue — iOS 잔재. 브랜드색은 deep blue 900 이다.',
        '#747480' => 'Fill Color / Quarternary — iOS 잔재. fill-* 는 Cool gray/600 기반이다.',
    ];

    private function tokensCss(): string
    {
        return strtolower(file_get_contents(resource_path('css/tokens.css')));
    }

    /** @return array<string,string> Figma 변수명 => 값 */
    private function figmaVars(): array
    {
        $path = resource_path('design/gpro-tokens.json');

        $this->assertFileExists($path, 'Figma 추출본이 없다. Dev Mode MCP 로 다시 뽑아야 한다.');

        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    /** @return array<string,string> 색상 변수만 */
    private function figmaColors(): array
    {
        return array_filter(
            $this->figmaVars(),
            fn ($v) => is_string($v) && str_starts_with($v, '#')
        );
    }

    /**
     * 채택한 GPRO 색은 전부 원시 계층에 있어야 한다.
     * Figma 에서 값이 바뀌면 여기서 잡힌다.
     */
    public function test_every_adopted_figma_color_is_in_the_primitive_layer(): void
    {
        $css = $this->tokensCss();
        $missing = [];

        foreach ($this->figmaColors() as $name => $hex) {
            $hex = strtolower($hex);

            if (isset(self::DELIBERATELY_UNUSED[$hex])) {
                continue;
            }

            if (! str_contains($css, $hex)) {
                $missing[$name] = $hex;
            }
        }

        $this->assertSame(
            [],
            $missing,
            'GPRO 원본에 있는데 tokens.css 에 없는 색: '.json_encode($missing, JSON_UNESCAPED_UNICODE)
        );
    }

    /** 일부러 뺀 iOS 잔재가 슬그머니 다시 들어오지 않았는지. */
    public function test_ios_leftovers_stay_out(): void
    {
        $css = $this->tokensCss();

        foreach (self::DELIBERATELY_UNUSED as $hex => $why) {
            $this->assertStringNotContainsString($hex, $css, "{$hex} 가 다시 들어왔다. {$why}");
        }
    }

    /**
     * 브랜드색은 검정 — GPRO Warm gray/900 이다. 바뀌면 의도적으로만 바뀌어야 한다.
     * hover(strong)는 Mono/Black, active(heavy)는 Mono/900. 검정은 더 어두워질 수 없어
     * 눌림 상태를 밝게 잡았다. 이 순서가 뒤집히면 버튼이 눌린 티가 안 난다.
     */
    public function test_brand_is_the_gpro_black_scale(): void
    {
        $vars = $this->figmaVars();
        $css = $this->tokensCss();

        $scale = [
            '--color-primary' => 'Warm gray/900',
            '--color-primary-normal' => 'Warm gray/900',
            '--color-primary-strong' => 'Mono/Black',
            '--color-primary-heavy' => 'Mono/900',
            '--color-primary-surface' => 'Warm gray/200',
            '--color-primary-soft' => 'Warm gray/100',
        ];

        foreach ($scale as $var => $figmaName) {
            $expected = strtolower($vars[$figmaName]);

            $this->assertMatchesRegularExpression(
                '/'.preg_quote($var, '/').':\s*'.preg_quote($expected, '/').'\b/',
                $css,
                "{$var} 가 GPRO {$figmaName}({$expected}) 이 아니다."
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

    /** 본문 스케일의 px 값은 GPRO Pretendard 단계 안에서만 고른다. */
    public function test_type_sizes_come_from_the_gpro_scale(): void
    {
        $gproSizes = [];

        foreach ($this->figmaVars() as $name => $value) {
            if (is_string($value) && str_starts_with($value, 'Font(') && str_contains($name, 'Pretendard')) {
                preg_match('/size: (\d+)/', $value, $m);
                $gproSizes[] = (int) $m[1];
            }
        }

        $gproSizes = array_unique($gproSizes);
        $this->assertNotEmpty($gproSizes, 'GPRO 추출본에 Pretendard 폰트 단계가 없다.');

        preg_match_all('/--text-[a-z0-9-]+:\s*(\d+)px/', $this->tokensCss(), $m);
        $ours = array_unique(array_map('intval', $m[1]));

        $offScale = array_values(array_diff($ours, $gproSizes));

        $this->assertSame(
            [],
            $offScale,
            'GPRO 스케일에 없는 폰트 크기를 쓰고 있다: '.implode(', ', $offScale).'px'
        );
    }

    /**
     * GPRO 에 대응이 없어 계산해 채운 값은 반드시 [파생] 표시를 달아 둔다.
     * 표시가 없으면 나중에 "Figma 에서 온 값"으로 오해하고 그대로 신뢰하게 된다.
     */
    public function test_derived_values_are_marked(): void
    {
        $css = file_get_contents(resource_path('css/tokens.css'));

        $derived = [
            '--color-line-normal-normal',
            '--color-fill-strong',
            '--color-material-dimmer',
            '--color-accent-fg-red',
        ];

        foreach ($derived as $var) {
            preg_match('/'.preg_quote($var, '/').':[^;]+;([^\n]*)/', $css, $m);

            $this->assertStringContainsString(
                '[파생]',
                $m[1] ?? '',
                "{$var} 는 GPRO 원본에 없는 파생값인데 [파생] 표시가 없다."
            );
        }
    }
}
