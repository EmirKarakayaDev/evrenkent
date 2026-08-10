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

        abort_unless(
            $book->status === ContentStatus::Yayinda || ($user && $user->id === $book->author_id),
            404
        );

        $book->load('author');

        $hasFavorited = $user?->hasFavorited($book) ?? false;
        $readingListItem = $user?->readingListItemFor($book);
        $hasPurchased = $user?->hasPurchased($book) ?? false;

        return view('books.show', compact('book', 'hasFavorited', 'readingListItem', 'hasPurchased'));
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
