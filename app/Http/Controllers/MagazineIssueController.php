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
        $isOwnerOrAdmin = $user && ($user->id === $magazineIssue->editor_id || $user->hasRole('super_admin'));

        abort_unless($magazineIssue->status === ContentStatus::Yayinda || $isOwnerOrAdmin, 404);

        $magazineIssue->load('editor');

        // Sahibi/Süper Admin önizlerken sayının içindeki taslak makaleleri de görebilir
        // (aksi halde onay bekleyen bir sayı hep boş görünürdü) — herkes için hâlâ sadece
        // yayınlanmış makaleler.
        $articles = $magazineIssue->articles()
            ->when(! $isOwnerOrAdmin, fn ($query) => $query->where('status', ContentStatus::Yayinda))
            ->with('author')
            ->get();

        return view('magazines.show', ['issue' => $magazineIssue, 'articles' => $articles]);
    }
}
