@props([
    // 결재선. 각 항목은 배열 또는 객체:
    //   name(필수) · role · status(ApprovalStepStatus|string) · at(처리시각) · comment(의견)
    'steps' => [],
    'title' => '결재선',
    'showTitle' => true,
])

@php
    use App\Enums\ApprovalStepStatus;

    $rows = collect($steps)
        ->values()
        ->map(function ($step, $i) {
            $get = fn(string $key, $default = null) => is_array($step)
                ? ($step[$key] ?? $default)
                : ($step->{$key} ?? $default);

            $raw = $get('status', ApprovalStepStatus::Waiting);

            return [
                'no' => $i + 1,
                'name' => (string) $get('name', '—'),
                'role' => $get('role'),
                'at' => $get('at'),
                'comment' => $get('comment'),
                'status' => $raw instanceof ApprovalStepStatus ? $raw : ApprovalStepStatus::from((string) $raw),
            ];
        });

    $last = $rows->count() - 1;
@endphp

<div {{ $attributes->class('flex flex-col gap-3') }}>
    @if ($showTitle)
        <div class="flex items-baseline gap-2">
            <h3 class="text-headline-2 font-semibold text-label-normal">{{ $title }}</h3>
            <span class="text-label-2 text-label-alternative">{{ $rows->count() }}단계</span>
        </div>
    @endif

    @if ($rows->isEmpty())
        <p class="text-body-2 text-label-alternative">지정된 결재자가 없습니다.</p>
    @else
        <ol class="flex flex-col">
            @foreach ($rows as $i => $row)
                @php $st = $row['status']; @endphp
                <li class="flex gap-3">
                    {{-- 마커 + 연결선 --}}
                    <div class="flex flex-col items-center">
                        <span
                            class="flex size-8 shrink-0 items-center justify-center rounded-full border text-label-2 font-semibold {{ $st->markerClasses() }}"
                            aria-hidden="true"
                        >
                            @if ($icon = $st->icon())
                                <x-dynamic-component :component="'icon-' . $icon" class="h-4 w-4" />
                            @else
                                {{ $row['no'] }}
                            @endif
                        </span>

                        @if ($i !== $last)
                            {{-- 진행이 멈춘 단계 뒤로는 연결선을 흐리게 — 뒤 결재자는 처리되지 않는다 --}}
                            <span
                                class="my-1 w-px flex-1 {{ $st->blocksProgress() ? 'bg-line-solid-alternative' : 'bg-line-solid-normal' }}"
                            ></span>
                        @endif
                    </div>

                    {{-- 본문 --}}
                    <div class="flex-1 {{ $i !== $last ? 'pb-5' : '' }}">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                            <span class="text-body-2 font-semibold text-label-normal">{{ $row['name'] }}</span>
                            @if ($row['role'])
                                <span class="text-label-2 text-label-alternative">{{ $row['role'] }}</span>
                            @endif
                            <x-badge size="xs" :color="$st->badgeColor()">{{ $st->label() }}</x-badge>
                        </div>

                        @if ($row['at'])
                            <p class="mt-0.5 text-caption-1 text-label-assistive">{{ $row['at'] }}</p>
                        @endif

                        @if ($row['comment'])
                            <p class="mt-1.5 rounded-md bg-fill-alternative px-3 py-2 text-label-1 text-label-neutral">
                                {{ $row['comment'] }}
                            </p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</div>
