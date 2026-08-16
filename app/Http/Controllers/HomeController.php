<?php

namespace App\Http\Controllers;

use App\Enums\BookShelf;
use App\Models\Book;
use App\Models\MagazineIssue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $shelf = BookShelf::tryFrom((string) $request->query('raf')) ?? BookShelf::YeniCikanlar;

        $books = $shelf->query()->take(6)->get();
        $totalBooks = Book::published()->count();
        $totalIssues = MagazineIssue::published()->count();

        return view('home', [
            'books' => $books,
            'shelf' => $shelf,
            'totalBooks' => $totalBooks,
            'totalIssues' => $totalIssues,
        ]);
    }
}
