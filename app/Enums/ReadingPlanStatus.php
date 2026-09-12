<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case Want = 'want';
    case Completed = 'completed';
    case Expired = 'expired';

    public function label()
    {
        switch ($this) {
            case self::Want:
                return '読みたい';

            case self::Completed:
                return '読了';

            case self::Expired:
                return '期日超え';
        }
    }
    public function badgeClass(): string
    {
        return match ($this) {
            self::Want => 'bg-blue-200 text-blue-600',
            self::Completed => 'bg-green-100 text-green-600',
            self::Expired => 'bg-red-200 text-red-100'
        };
    }
}
