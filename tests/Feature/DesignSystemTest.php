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
