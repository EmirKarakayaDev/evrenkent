<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

trait RecordsContentReview
{
    /**
     * Bir içerik (Book/Article/MagazineIssue) üzerindeki onay/red/yayınlama aksiyonunu
     * content_reviews tablosuna işler. $record'un morphMany 'reviews' ilişkisi olması beklenir.
     */
    protected static function recordReview(Model $record, string $action, ?string $note = null): void
    {
        $record->reviews()->create([
            'reviewer_id' => auth()->id(),
            'action' => $action,
            'note' => $note,
        ]);
    }
}
