<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    public function test_root_redirects_to_styleguide(): void
    {
        $this->get('/')->assertRedirect('/styleguide');
    }

    /**
     * 스타일가이드는 모든 컴포넌트를 실제로 렌더한다. 이 테스트가 통과하면
     * 컴포넌트·아이콘·enum 중 어느 것도 렌더 단계에서 터지지 않는다는 뜻이다.
     */
    public function test_styleguide_renders_every_component(): void
    {
        $this->get('/styleguide')
            ->assertOk()
            ->assertSee('디자인 시스템');
    }

    public function test_styleguide_lists_the_whole_ds_icon_set(): void
    {
        $onDisk = count(glob(resource_path('svg/icons/*.svg')));

        $this->assertSame(219, $onDisk, 'DS 아이콘 세트가 219종이 아니다.');
        $this->get('/styleguide')->assertSee("아이콘 {$onDisk}종");
    }

    /**
     * DS 에 없어서 만든 확장 아이콘(resources/svg/ext)도 스타일가이드에 다 나와야 한다.
     *
     * DS 세트와 섞이지 않게 파일 위치·prefix 를 나눠 뒀다. 목록에 안 뜨면 다음 사람이
     * 같은 글리프를 또 만든다 — DS 219종을 먼저 뒤져 보라는 규칙이 그때 무력해진다.
     */
    public function test_styleguide_lists_the_extension_icons(): void
    {
        $names = array_map(
            fn ($p) => pathinfo($p, PATHINFO_FILENAME),
            glob(resource_path('svg/ext/*.svg'))
        );

        $this->assertNotEmpty($names, '확장 아이콘이 하나도 없다 — 세트를 지웠다면 이 테스트도 지울 것.');

        $page = $this->get('/styleguide')->assertOk()->assertSee('확장 아이콘 '.count($names).'종');

        foreach ($names as $name) {
            $page->assertSee($name);
        }
    }

    /**
     * 폰트는 Pretendard 하나만 쓴다.
     *
     * --font-mono 를 덮어두지 않으면 Tailwind preflight 가 code·kbd·samp·pre 에
     * 기본 모노스페이스 스택을 물려서 그 태그들만 다른 폰트로 튄다. 실제로 표의
     * 문서번호를 <code> 로 감싸 놓아 SF Mono 로 나온 적이 있다.
     */
    public function test_only_pretendard_is_used(): void
    {
        $css = file_get_contents(resource_path('css/tokens.css'));

        foreach (['--font-sans', '--font-mono'] as $var) {
            preg_match('/'.preg_quote($var, '/').':\s*([^;]+);/', $css, $m);
            $this->assertNotEmpty($m, "{$var} 가 tokens.css 에 없다.");

            $stack = $m[1];
            $this->assertStringContainsString('Pretendard', $stack, "{$var} 에 Pretendard 가 없다.");

            foreach (['Apple SD Gothic Neo', 'Noto Sans', 'Malgun', 'Helvetica', 'Roboto', 'Segoe', 'system-ui', 'ui-monospace', 'Menlo', 'Consolas'] as $other) {
                $this->assertStringNotContainsString(
                    $other,
                    $stack,
                    "{$var} 에 Pretendard 아닌 폰트({$other})가 있다. 폰트는 Pretendard 하나만 쓴다."
                );
            }
        }

        // font-mono 유틸리티는 이제 Pretendard 로 렌더된다 — 이름이 거짓이 되므로 쓰지 않는다.
        $offenders = [];

        foreach ($this->bladeFiles() as $file) {
            if (str_contains(file_get_contents($file), 'font-mono')) {
                $offenders[] = str_replace(resource_path('views/'), '', $file);
            }
        }

        $this->assertSame([], $offenders, 'font-mono 를 쓰고 있다. 폰트가 하나뿐이므로 이 유틸리티는 의미가 없다: '.implode(', ', $offenders));
    }

    /**
     * 빌드된 CSS 에도 Pretendard 아닌 폰트 이름이 남아 있으면 안 된다.
     *
     * Tailwind preflight 는 var(--default-font-family, <긴 폴백>) 형태로 깔기 때문에
     * 소스만 고쳐도 산출물에는 Noto Sans·Arial·이모지 폰트 이름이 문자열로 남는다.
     * vite.config.js 의 strip-dead-font-fallbacks 플러그인이 그 폴백을 잘라낸다.
     * 이 테스트는 그 플러그인이 실제로 동작했는지 본다.
     */
    public function test_built_css_has_no_other_font_names(): void
    {
        $files = glob(public_path('build/assets/app-*.css'));

        if ($files === []) {
            $this->markTestSkipped('빌드 산출물이 없다 — npm run build 먼저.');
        }

        $forbidden = [
            'Noto Sans', 'Noto Color Emoji', 'Apple Color Emoji', 'Segoe UI',
            'Arial', 'Helvetica', 'Roboto', '-apple-system', 'BlinkMacSystemFont',
            'ui-monospace', 'SFMono', 'Menlo', 'Monaco', 'Consolas', 'Courier',
        ];

        foreach ($files as $file) {
            $css = file_get_contents($file);

            foreach ($forbidden as $name) {
                $this->assertStringNotContainsString(
                    $name,
                    $css,
                    basename($file)." 에 Pretendard 아닌 폰트 이름({$name})이 있다. ".
                    'vite.config.js 의 strip-dead-font-fallbacks 가 동작하는지 확인할 것.'
                );
            }

            $this->assertStringContainsString('--font-sans:"Pretendard Variable"', $css);
            $this->assertStringContainsString('--font-mono:"Pretendard Variable"', $css);
        }
    }

    /**
     * 원본은 각진 시스템이다 — 실측 반경이 2·3·4·6px, Card·Checkbox 는 직각.
     * Tailwind 기본 반경(rounded-xl 12px · rounded-2xl 16px …)이나 임의값이 섞이면
     * 화면마다 모서리가 제각각이 된다. DS 4단 + full/none 만 허용한다.
     */
    public function test_views_only_use_the_ds_radius_scale(): void
    {
        $allowed = ['rounded-xs', 'rounded-sm', 'rounded-md', 'rounded-lg', 'rounded-full', 'rounded-none'];
        $offScale = [];

        foreach ($this->bladeFiles() as $file) {
            preg_match_all('/\brounded(?:-[a-z0-9\[\]#%.-]+)?\b/', file_get_contents($file), $m);

            foreach (array_unique($m[0]) as $cls) {
                if (! in_array($cls, $allowed, true)) {
                    $offScale[] = basename($file).' : '.$cls;
                }
            }
        }

        $this->assertSame(
            [],
            $offScale,
            'DS 밖 모서리 반경을 쓰고 있다 (허용: '.implode(', ', $allowed).') — '
                .implode(' / ', $offScale)
        );
    }

    /** @return string[] */
    private function bladeFiles(): array
    {
        $dir = new \RecursiveDirectoryIterator(resource_path('views'));
        $files = [];

        foreach (new \RecursiveIteratorIterator($dir) as $f) {
            if ($f->isFile() && str_ends_with($f->getFilename(), '.blade.php')) {
                $files[] = $f->getPathname();
            }
        }

        return $files;
    }

    /** 뷰에 raw hex 가 새로 들어오는 것을 막는다. 색은 토큰에서만 온다. */
    public function test_no_raw_hex_colors_in_views(): void
    {
        $offenders = [];

        // glob 의 ** 는 재귀가 아니라서 components/table/* 을 놓친다 — 직접 순회한다.
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))),
            '/\.blade\.php$/'
        );

        foreach ($files as $file) {
            $body = file_get_contents($file->getPathname());

            if (preg_match_all('/#[0-9a-fA-F]{3,8}\b/', $body, $m)) {
                $offenders[str_replace(resource_path('views/'), '', $file->getPathname())] = array_values(array_unique($m[0]));
            }
        }

        $this->assertSame([], $offenders, 'Blade 뷰에 raw hex 가 있다. tokens.css 의 토큰을 써야 한다: '.json_encode($offenders));
    }
}
