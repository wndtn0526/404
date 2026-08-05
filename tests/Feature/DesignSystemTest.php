<?php

namespace Tests\Feature;

use App\Enums\ApprovalStepStatus;
use App\Enums\DocumentStatus;
use Illuminate\Support\Facades\Blade;
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
            ->assertSee('전자결재 디자인 시스템')
            ->assertSee('결재선')
            ->assertSee('반려로 멈춘 결재선');
    }

    public function test_styleguide_lists_the_whole_ds_icon_set(): void
    {
        $onDisk = count(glob(resource_path('svg/icons/*.svg')));

        $this->assertSame(219, $onDisk, 'DS 아이콘 세트가 219종이 아니다.');
        $this->get('/styleguide')->assertSee("아이콘 {$onDisk}종");
    }

    /**
     * GPRO 는 각진 시스템이다 — 실측 반경이 2·3·4·6px, Card·Checkbox 는 직각.
     * Tailwind 기본 반경(rounded-xl 12px · rounded-2xl 16px …)이나 임의값이 섞이면
     * 화면마다 모서리가 제각각이 된다. DS 4단 + full/none 만 허용한다.
     */
    public function test_views_only_use_the_gpro_radius_scale(): void
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

    public function test_doc_status_component_renders_label_and_ds_color(): void
    {
        $html = Blade::render('<x-doc-status :status="$s" />', ['s' => DocumentStatus::Rejected]);

        $this->assertStringContainsString('반려', $html);
        // red 계열은 DS accent foreground 토큰을 타야 한다 (raw hex 금지)
        $this->assertStringContainsString('accent-fg-red', $html);
        $this->assertStringNotContainsString('#', $html);
    }

    public function test_doc_status_accepts_a_plain_string(): void
    {
        $html = Blade::render('<x-doc-status status="approved" />');

        $this->assertStringContainsString('승인', $html);
    }

    public function test_approval_line_renders_each_step_with_its_status(): void
    {
        $html = Blade::render('<x-approval-line :steps="$steps" />', [
            'steps' => [
                ['name' => '김기안', 'role' => '사원', 'status' => ApprovalStepStatus::Approved, 'at' => '2026-07-31 09:00'],
                ['name' => '박부장', 'role' => '부장', 'status' => ApprovalStepStatus::Current],
            ],
        ]);

        $this->assertStringContainsString('김기안', $html);
        $this->assertStringContainsString('박부장', $html);
        $this->assertStringContainsString('결재 차례', $html);
        $this->assertStringContainsString('2단계', $html);
    }

    public function test_approval_line_handles_an_empty_line(): void
    {
        $html = Blade::render('<x-approval-line :steps="[]" />');

        $this->assertStringContainsString('지정된 결재자가 없습니다', $html);
    }

    /** 상태 전이는 화면이 아니라 enum 이 막는다. */
    public function test_document_status_blocks_illegal_transitions(): void
    {
        $this->assertTrue(DocumentStatus::Draft->canTransitionTo(DocumentStatus::Pending));
        $this->assertTrue(DocumentStatus::Pending->canTransitionTo(DocumentStatus::Rejected));
        $this->assertTrue(DocumentStatus::Approved->canTransitionTo(DocumentStatus::Completed));

        // 기안에서 곧바로 완료로 점프할 수 없다
        $this->assertFalse(DocumentStatus::Draft->canTransitionTo(DocumentStatus::Completed));
        $this->assertFalse(DocumentStatus::Draft->canTransitionTo(DocumentStatus::Approved));

        // 종료 상태에서는 어디로도 갈 수 없다
        foreach ([DocumentStatus::Rejected, DocumentStatus::Withdrawn, DocumentStatus::Completed] as $terminal) {
            $this->assertTrue($terminal->isTerminal());
            $this->assertSame([], $terminal->allowedTransitions());

            foreach (DocumentStatus::cases() as $next) {
                $this->assertFalse($terminal->canTransitionTo($next));
            }
        }
    }

    public function test_only_a_draft_is_editable(): void
    {
        foreach (DocumentStatus::cases() as $status) {
            $this->assertSame($status === DocumentStatus::Draft, $status->isEditable());
        }
    }

    /** 모든 상태가 라벨·배지색·아이콘을 갖는다 — 새 상태를 추가하고 매핑을 빼먹으면 여기서 걸린다. */
    public function test_every_status_has_presentation_metadata(): void
    {
        $badgeColors = ['neutral', 'primary', 'blue', 'green', 'red', 'cyan', 'orange', 'violet'];

        foreach (DocumentStatus::cases() as $status) {
            $this->assertNotSame('', $status->label());
            $this->assertContains($status->badgeColor(), $badgeColors, "{$status->value} 의 배지색이 DS 팔레트 밖이다.");
            $this->assertFileExists(resource_path("svg/icons/{$status->icon()}.svg"));
        }

        foreach (ApprovalStepStatus::cases() as $step) {
            $this->assertNotSame('', $step->label());
            $this->assertContains($step->badgeColor(), $badgeColors);
            $this->assertNotSame('', $step->markerClasses());

            if ($icon = $step->icon()) {
                $this->assertFileExists(resource_path("svg/icons/{$icon}.svg"));
            }
        }
    }

    /** 반려·보류에서 결재선이 멈춘다. */
    public function test_rejection_and_hold_block_progress(): void
    {
        $this->assertTrue(ApprovalStepStatus::Rejected->blocksProgress());
        $this->assertTrue(ApprovalStepStatus::Held->blocksProgress());
        $this->assertFalse(ApprovalStepStatus::Approved->blocksProgress());
        $this->assertFalse(ApprovalStepStatus::Waiting->blocksProgress());
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
