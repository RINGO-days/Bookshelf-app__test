<?php

namespace App\Models;

use App\Enums\ReadingPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property Carbon $target_date
 * @property Carbon $compleated_at
 * @property string $status
 *
 * @property-read Book|null $book
 * @property-read User|null $user
 */
class ReadingPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'book_id',
        'target_date',
        'completed_at',
        'status'
    ];
    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'datetime',
        'status' => ReadingPlanStatus::class,
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
