<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'author_id', 'title', 'slug', 'description',
        'cover_image', 'price', 'discount_price', 'status',
        'is_editors_pick', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'published_at' => 'datetime',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'is_editors_pick' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_book');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(ContentReview::class, 'reviewable');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Yayinda);
    }

    /** En çok satın alınandan aza doğru sıralar (Çok Satanlar). */
    public function scopeBestsellers(Builder $query): Builder
    {
        return $query->withCount('purchases')->orderByDesc('purchases_count');
    }

    public function scopeEditorsPick(Builder $query): Builder
    {
        return $query->where('is_editors_pick', true);
    }

    /** Sadece indirimli fiyatı olan (Fırsatlar) kitaplar. */
    public function scopeOnSale(Builder $query): Builder
    {
        return $query->whereNotNull('discount_price');
    }
}
