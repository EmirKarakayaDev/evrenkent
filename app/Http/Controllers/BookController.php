<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Book;
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
}
