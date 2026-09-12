<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Book;

/**
 * @property int $id
 * @property string $name
 *
 * @property-read Collection<int,Book> $books
 */
class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class);
    }
}
