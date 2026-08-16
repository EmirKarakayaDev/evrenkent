<?php

namespace App\Http\Controllers;

use App\Enums\BookShelf;
use App\Models\Book;
use App\Models\MagazineIssue;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Anasayfa artık tek bir türe kilitli bir vitrin değil, çok raflı bir keşif
        // sayfası — her raf kendi "Tümünü Gör" linkiyle ilgili katalog sayfasına açılır.
        // Boş bir raf hiç gösterilmez (dağınık/boş kutularla karşılaşılmasın diye).
        $shelves = collect(BookShelf::cases())
            ->mapWithKeys(fn (BookShelf $shelf) => [$shelf->value => $shelf->query()->take(6)->get()])
            ->filter(fn ($books) => $books->isNotEmpty());

        $issues = MagazineIssue::published()->with('editor')->latest('publish_date')->take(6)->get();

        $totalBooks = Book::published()->count();
        $totalIssues = MagazineIssue::published()->count();

        return view('home', [
            'shelves' => $shelves,
            'issues' => $issues,
            'totalBooks' => $totalBooks,
            'totalIssues' => $totalIssues,
        ]);
    }
}
