<?php

namespace App\Http\Controllers;

use App\Enums\BookShelf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCatalogController extends Controller
{
    public function index(Request $request): View
    {
        $shelf = BookShelf::tryFrom((string) $request->query('raf')) ?? BookShelf::YeniCikanlar;

        $books = $shelf->query()->paginate(18)->withQueryString();

        return view('books.index', ['books' => $books, 'shelf' => $shelf]);
    }
}
