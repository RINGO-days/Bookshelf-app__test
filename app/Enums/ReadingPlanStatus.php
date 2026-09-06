<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case Want = 'want';
    case Completed = 'completed';

    public function label()
    {
        switch ($this) {
            case self::Want:
                return '読みたい';
            case self::Completed:
                return '読了';
        }
    }
    public function badgeClass(): string
    {
        return match ($this) {
            self::Want => 'bg-blue-200 text-blue-600',
            self::Completed => 'bg-green-100 text-green-600',
        };
    }
}
