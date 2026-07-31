<?php

namespace Tests\Feature;

use Livewire\Livewire;
use Tests\TestCase;

/**
 * 개발환경 스모크 테스트. health-check 컴포넌트와 함께 지운다.
 */
class HealthTest extends TestCase
{
    public function test_health_page_renders(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertSee('개발환경 점검');
    }

    public function test_health_check_probes_pass(): void
    {
        Livewire::test('health-check')
            ->assertViewHas('results', function (array $results) {
                foreach ($results as $label => $result) {
                    $this->assertTrue($result['ok'], "{$label} 점검 실패: {$result['detail']}");
                }

                return true;
            });
    }

    public function test_livewire_round_trip_increments_counter(): void
    {
        Livewire::test('health-check')
            ->assertSet('checks', 1)
            ->call('recheck')
            ->assertSet('checks', 2);
    }
}
