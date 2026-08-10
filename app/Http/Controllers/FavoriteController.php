<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favorites = auth()->user()->favorites()
            ->with('favoritable')
            ->latest()
            ->get();

        return view('panel.favorilerim.index', compact('favorites'));
    }

    public function toggleBook(Book $book): RedirectResponse
    {
        $user = auth()->user();

        $favorite = $user->favorites()
            ->where('favoritable_type', Book::class)
            ->where('favoritable_id', $book->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('status', 'Favorilerden çıkarıldı.');
        }

        $user->favorites()->create([
            'favoritable_type' => Book::class,
            'favoritable_id' => $book->id,
        ]);

        return back()->with('status', 'Favorilere eklendi.');
    }

    public function destroy(Favorite $favorite): RedirectResponse
    {
        $this->authorize('delete', $favorite);

        $favorite->delete();

        return back()->with('status', 'Favorilerden çıkarıldı.');
    }
}
