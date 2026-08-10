<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id', 'magazine_issue_id', 'title', 'slug',
        'content', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function magazineIssue(): BelongsTo
    {
        return $this->belongsTo(MagazineIssue::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_article');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(ContentReview::class, 'reviewable');
    }
}
