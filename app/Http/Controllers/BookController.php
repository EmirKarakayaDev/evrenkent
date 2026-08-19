<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Enums\ReadingStatus;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookController extends Controller
{
    public function show(Book $book): View
    {
        $user = auth()->user();

        // "Onaylandı" + hedef tarihi olan kitaplar bir teaser (Yakında Çıkacak) olarak
        // herkese açık — henüz satın alma/okuma yok, sadece tanıtım. Tarihsiz "Onaylandı"
        // kitaplar hâlâ sadece yazarına görünür (henüz kamuya duyurulmaya hazır değil).
        abort_unless(
            $book->status === ContentStatus::Yayinda
                || ($book->status === ContentStatus::Onaylandi && $book->scheduled_publish_at)
                || ($user && $user->id === $book->author_id)
                || ($user && $user->hasRole('super_admin')),
            404
        );

        $book->load(['author', 'categories']);

        $isUpcoming = $book->status === ContentStatus::Onaylandi && $book->scheduled_publish_at !== null;
        $hasFavorited = $user?->hasFavorited($book) ?? false;
        $readingListItem = $user?->readingListItemFor($book);
        $hasPurchased = $user?->hasPurchased($book) ?? false;
        $hasInCart = $user?->hasInCart($book) ?? false;
        $chapterCount = $book->chapters()->count();

        // Öneri şeridi: önce aynı kategorideki başka yayınlanmış kitaplar; yeterli sayıda yoksa
        // aynı yazarın diğer kitaplarıyla tamamlanır (hiçbiri yoksa şerit hiç gösterilmez, uydurma yok).
        $relatedBooks = collect();

        if ($book->categories->isNotEmpty()) {
            $relatedBooks = Book::published()
                ->where('id', '!=', $book->id)
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $book->categories->pluck('id')))
                ->with('author')
                ->latest('published_at')
                ->take(6)
                ->get();
        }

        if ($relatedBooks->count() < 6) {
            $relatedBooks = $relatedBooks->concat(
                Book::published()
                    ->where('id', '!=', $book->id)
                    ->where('author_id', $book->author_id)
                    ->whereNotIn('id', $relatedBooks->pluck('id'))
                    ->with('author')
                    ->latest('published_at')
                    ->take(6 - $relatedBooks->count())
                    ->get()
            );
        }

        return view('books.show', compact('book', 'isUpcoming', 'hasFavorited', 'readingListItem', 'hasPurchased', 'hasInCart', 'chapterCount', 'relatedBooks'));
    }

    public function read(Book $book, ?int $chapterNumber = null): View|RedirectResponse
    {
        $user = auth()->user();
        $isAuthor = $user && $user->id === $book->author_id;

        abort_unless($book->status === ContentStatus::Yayinda || $isAuthor, 404);

        $locked = $book->price > 0 && ! ($isAuthor || ($user && $user->hasPurchased($book)));

        if ($locked) {
            return redirect()->route('kitaplar.show', $book)
                ->with('status', 'Bu kitabı okumak için satın almanız gerekiyor.');
        }

        $chapters = $book->chapters;
        $readingListItem = $user?->readingListItemFor($book);

        if ($chapterNumber === null) {
            $chapterNumber = $readingListItem?->last_chapter_number
                ?? $chapters->first()?->order;
        }

        $chapter = $chapterNumber ? $chapters->firstWhere('order', $chapterNumber) : null;

        if ($chapterNumber !== null && ! $chapter) {
            abort(404);
        }

        if ($user && $chapter) {
            $readingListItem = $user->readingListItems()->firstOrCreate([
                'readable_type' => Book::class,
                'readable_id' => $book->id,
            ], [
                'status' => ReadingStatus::Listede,
            ]);
            $readingListItem->update(['last_chapter_number' => $chapter->order]);
        }

        $prevChapter = $chapter ? $chapters->where('order', '<', $chapter->order)->last() : null;
        $nextChapter = $chapter ? $chapters->where('order', '>', $chapter->order)->first() : null;

        return view('books.read', compact('book', 'chapters', 'chapter', 'prevChapter', 'nextChapter', 'readingListItem'));
    }
}
