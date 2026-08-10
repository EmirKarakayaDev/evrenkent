<?php

namespace App\Models;

use App\Enums\ReadingStatus;
use Database\Factories\ReadingListItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReadingListItem extends Model
{
    /** @use HasFactory<ReadingListItemFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'readable_type', 'readable_id', 'status', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReadingStatus::class,
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function readable(): MorphTo
    {
        return $this->morphTo();
    }
}
