<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Expired = 'expired';

    public function label()
    {
        switch ($this) {
            case self::InProgress:
                return '進行中';

            case self::Completed:
                return '完了';

            case self::Expired:
                return '期日切れ';
        }
    }
    public function badgeClass(): string
    {
        return match ($this) {
            self::InProgress => 'bg-blue-200 text-blue-600',
            self::Completed => 'bg-green-100 text-green-600',
            self::Expired => 'bg-red-200 text-red-800'
        };
    }
}
