<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * 결재 문서 상태 — 단일 정의 지점.
 *
 * 상태 전이는 반드시 canTransitionTo() 를 통과해야 한다. 화면에서 버튼을 숨기는 것으로
 * 대신하지 말고, 상태를 바꾸는 서비스가 이 메서드로 검사한다. 임의의 상태 점프
 * (예: 기안 → 완료)를 여기서 막지 않으면 결재 이력이 의미를 잃는다.
 */
enum DocumentStatus: string
{
    /** 작성 중. 아직 결재선에 올라가지 않았다. */
    case Draft = 'draft';

    /** 상신됨. 첫 결재자의 처리를 기다린다. */
    case Pending = 'pending';

    /** 중간 결재자까지 승인. 남은 결재자가 있다. */
    case InProgress = 'in_progress';

    /** 결재선 전원 승인. */
    case Approved = 'approved';

    /** 결재자 중 한 명이 반려. */
    case Rejected = 'rejected';

    /** 기안자가 상신을 회수. */
    case Withdrawn = 'withdrawn';

    /** 승인 후 후속 처리까지 종료(보관). */
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => '기안',
            self::Pending => '결재 대기',
            self::InProgress => '진행 중',
            self::Approved => '승인',
            self::Rejected => '반려',
            self::Withdrawn => '회수',
            self::Completed => '완료',
        };
    }

    /** x-badge 의 color prop 값. DS 배지 팔레트를 벗어나지 않는다. */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft, self::Withdrawn => 'neutral',
            self::Pending => 'orange',
            self::InProgress => 'blue',
            self::Approved, self::Completed => 'green',
            self::Rejected => 'red',
        };
    }

    /** DS 아이콘 세트(resources/svg/icons) 의 이름. */
    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'document',
            self::Pending => 'clock',
            self::InProgress => 'circle-dot',
            self::Approved => 'circle-check',
            self::Rejected => 'circle-close',
            self::Withdrawn => 'circle-block',
            self::Completed => 'check',
        };
    }

    /** 더 이상 상태가 바뀌지 않는 종료 상태인가. */
    public function isTerminal(): bool
    {
        return in_array($this, [self::Rejected, self::Withdrawn, self::Completed], true);
    }

    /** 기안자가 아직 내용을 고칠 수 있는 상태인가. */
    public function isEditable(): bool
    {
        return $this === self::Draft;
    }

    /**
     * 허용된 다음 상태 목록.
     *
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Draft => [self::Pending],
            self::Pending => [self::InProgress, self::Approved, self::Rejected, self::Withdrawn],
            self::InProgress => [self::Approved, self::Rejected, self::Withdrawn],
            self::Approved => [self::Completed],
            // 반려·회수·완료는 종료 상태. 재상신은 새 문서(또는 새 결재 회차)로 만든다.
            self::Rejected, self::Withdrawn, self::Completed => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }
}
