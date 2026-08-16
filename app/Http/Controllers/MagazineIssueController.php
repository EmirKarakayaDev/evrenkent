<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\MagazineIssue;
use Illuminate\View\View;

class MagazineIssueController extends Controller
{
    public function show(MagazineIssue $magazineIssue): View
    {
        $user = auth()->user();

        abort_unless(
            $magazineIssue->status === ContentStatus::Yayinda || ($user && $user->id === $magazineIssue->editor_id),
            404
        );

        $magazineIssue->load('editor');

        $articles = $magazineIssue->articles()
            ->where('status', ContentStatus::Yayinda)
            ->with('author')
            ->get();

        return view('magazines.show', ['issue' => $magazineIssue, 'articles' => $articles]);
    }
}
