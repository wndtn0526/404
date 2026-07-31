<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * 개발환경 스모크 테스트.
 * PostgreSQL / Redis / Livewire 왕복이 실제로 동작하는지 한 화면에서 확인한다.
 * 도메인 기능이 붙기 시작하면 지워도 되는 컴포넌트.
 */
new class extends Component
{
    public int $checks = 1;

    public function recheck(): void
    {
        $this->checks++;
    }

    public function with(): array
    {
        return [
            'results' => [
                'PostgreSQL' => $this->probe(fn () => DB::selectOne('select version()')->version),
                'Redis (cache)' => $this->probe(function () {
                    $token = 'ok-'.$this->checks;
                    Cache::put('health:ping', $token, 10);

                    if (Cache::get('health:ping') !== $token) {
                        throw new \RuntimeException('값이 되돌아오지 않음');
                    }

                    return 'put/get 왕복 성공';
                }),
                'Session driver' => $this->probe(fn () => config('session.driver')),
                'Queue driver' => $this->probe(fn () => config('queue.default')),
            ],
        ];
    }

    private function probe(callable $check): array
    {
        try {
            return ['ok' => true, 'detail' => (string) $check()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'detail' => $e->getMessage()];
        }
    }
};
?>

<div class="mx-auto max-w-2xl px-6 py-16">
    <h1 class="text-2xl font-bold text-slate-900">전자결재 · 개발환경 점검</h1>
    <p class="mt-1 text-sm text-slate-500">
        Laravel {{ app()->version() }} · PHP {{ PHP_VERSION }} · 점검 {{ $checks }}회
    </p>

    <dl class="mt-8 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
        @foreach ($results as $label => $result)
            <div class="flex items-start gap-4 px-5 py-4">
                <span @class([
                    'mt-1 inline-block size-2.5 shrink-0 rounded-full',
                    'bg-emerald-500' => $result['ok'],
                    'bg-rose-500' => ! $result['ok'],
                ])></span>
                <div class="min-w-0">
                    <dt class="text-sm font-semibold text-slate-900">{{ $label }}</dt>
                    <dd class="mt-0.5 break-words text-sm text-slate-600">{{ $result['detail'] }}</dd>
                </div>
            </div>
        @endforeach
    </dl>

    <button
        type="button"
        wire:click="recheck"
        wire:loading.attr="disabled"
        class="mt-6 rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-50"
    >
        다시 확인
    </button>
    <p class="mt-2 text-xs text-slate-400">
        버튼을 눌러 숫자가 올라가면 Livewire 왕복까지 정상이다.
    </p>
</div>
