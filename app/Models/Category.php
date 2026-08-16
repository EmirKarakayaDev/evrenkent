<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function books(): BelongsToMany
    {
        // Pivot tablo migration'da 'category_book' olarak oluşturuldu (Eloquent'in
        // alfabetik varsayılanı 'book_category' olurdu) — burada açıkça belirtiyoruz.
        return $this->belongsToMany(Book::class, 'category_book');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'category_article');
    }
}
