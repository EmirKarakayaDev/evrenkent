<?php

namespace App\Http\Controllers;

use App\Enums\BookShelf;
use App\Models\Book;
use App\Models\MagazineIssue;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Anasayfa mockup'ındaki gibi bir "tip anahtarı" (Kitaplar/Dergiler/Sözlükler):
     * her zaman görünür, biri seçili, seçime göre altındaki pil grubu + içerik değişir.
     */
    public function index(Request $request): View
    {
        $tur = $request->query('tur') === 'dergiler' ? 'dergiler' : 'kitaplar';

        $totalBooks = Book::published()->count();
        $totalIssues = MagazineIssue::published()->count();

        if ($tur === 'dergiler') {
            $issues = MagazineIssue::published()->with('editor')->latest('publish_date')->take(6)->get();

            return view('home', compact('tur', 'issues', 'totalBooks', 'totalIssues'));
        }

        $shelf = BookShelf::tryFrom((string) $request->query('raf')) ?? BookShelf::YeniCikanlar;
        $books = $shelf->query()->take(6)->get();

        return view('home', compact('tur', 'shelf', 'books', 'totalBooks', 'totalIssues'));
    }
}
