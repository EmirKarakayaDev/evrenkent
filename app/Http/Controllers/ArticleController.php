<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function show(Article $article): View
    {
        $user = auth()->user();

        abort_unless(
            $article->status === ContentStatus::Yayinda || ($user && $user->id === $article->author_id),
            404
        );

        $article->load(['author', 'magazineIssue']);

        return view('articles.show', compact('article'));
    }
}
