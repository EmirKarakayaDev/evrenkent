<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $books = Book::published()->latest('published_at')->take(6)->get();

        return view('home', ['books' => $books]);
    }
}
