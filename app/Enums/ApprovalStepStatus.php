<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * 결재선 한 단계(결재자 한 명)의 처리 상태.
 *
 * 문서 전체 상태(DocumentStatus)와는 별개다. 문서가 '진행 중'이어도 결재선 안에는
 * 이미 승인한 사람 · 지금 차례인 사람 · 아직 차례가 오지 않은 사람이 함께 있다.
 */
enum ApprovalStepStatus: string
{
    /** 아직 차례가 오지 않음. */
    case Waiting = 'waiting';

    /** 지금 이 사람의 처리를 기다린다. */
    case Current = 'current';

    case Approved = 'approved';
    case Rejected = 'rejected';

    /** 보류 — 처리를 미룸(결재선은 여기서 멈춘다). */
    case Held = 'held';

    /** 위임 등으로 이 단계를 건너뜀. */
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => '대기',
            self::Current => '결재 차례',
            self::Approved => '승인',
            self::Rejected => '반려',
            self::Held => '보류',
            self::Skipped => '생략',
        };
    }

    /** 스텝 원형 마커의 배경·테두리·글자색 (DS 토큰만 사용). */
    public function markerClasses(): string
    {
        return match ($this) {
            self::Waiting => 'bg-background-normal border-line-solid-normal text-label-assistive',
            self::Current => 'bg-primary-surface border-primary text-primary-strong',
            self::Approved => 'bg-status-positive border-status-positive text-white',
            self::Rejected => 'bg-status-negative border-status-negative text-white',
            self::Held => 'bg-status-cautionary border-status-cautionary text-white',
            self::Skipped => 'bg-fill-normal border-line-solid-normal text-label-assistive',
        };
    }

    /** 마커 안에 넣을 DS 아이콘. null 이면 순번 숫자를 쓴다. */
    public function icon(): ?string
    {
        return match ($this) {
            self::Approved => 'check',
            self::Rejected => 'close',
            self::Held => 'exclamation',
            self::Skipped => 'arrow-turn-down-right',
            self::Waiting, self::Current => null,
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Waiting, self::Skipped => 'neutral',
            self::Current => 'primary',
            self::Approved => 'green',
            self::Rejected => 'red',
            self::Held => 'orange',
        };
    }

    /** 이 단계에서 결재선 진행이 멈추는가. */
    public function blocksProgress(): bool
    {
        return in_array($this, [self::Rejected, self::Held], true);
    }
}
