<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'published_year',
        'isbn',
        'stock',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'stock' => 'integer',
    ];

    /**
     * The users that have borrowed this book.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_loans')
                    ->withPivot(['loaned_at', 'expected_return_at', 'returned_at'])
                    ->withTimestamps();
    }

    /**
     * Check if book is available for loan.
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Get available stock count.
     */
    public function getAvailableStockAttribute(): int
    {
        $loanedCount = $this->users()
            ->wherePivotNull('returned_at')
            ->count();

        return max(0, $this->stock - $loanedCount);
    }
}
