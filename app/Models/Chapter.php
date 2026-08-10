<?php

namespace App\Models;

use Database\Factories\ChapterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chapter extends Model
{
    /** @use HasFactory<ChapterFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id', 'title', 'content', 'order',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
