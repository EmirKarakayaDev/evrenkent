<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DergiYonetimiController extends Controller
{
    /**
     * Makale Havuzu sekmeleri — mockup'taki 5 sekmeyle birebir eşleşir. "Kabul Edilen"
     * hem Onaylandı hem Yayında olanları kapsar (mockup'ta ikisi ayrı değil).
     *
     * @return array<string, array{label: string, statuses: array<ContentStatus>|null}>
     */
    private function articleTabs(): array
    {
        return [
            'tumu' => ['label' => 'Tümü', 'statuses' => null],
            'incelenmeyi-bekleyen' => ['label' => 'İncelenmeyi Bekleyen', 'statuses' => [ContentStatus::Gonderildi, ContentStatus::Incelemede]],
            'revizyon-istenen' => ['label' => 'Revizyon İstenen', 'statuses' => [ContentStatus::RevizyonIstendi]],
            'kabul-edilen' => ['label' => 'Kabul Edilen', 'statuses' => [ContentStatus::Onaylandi, ContentStatus::Yayinda]],
            'reddedilen' => ['label' => 'Reddedilen', 'statuses' => [ContentStatus::Reddedildi]],
        ];
    }

    /**
     * Editörün kendi sayılarına bağlı tüm makaleler (başka editörünkiler hiç görünmez).
     */
    private function articlesQuery()
    {
        return Article::whereHas('magazineIssue', fn ($q) => $q->where('editor_id', auth()->id()))
            ->with(['author', 'magazineIssue', 'categories']);
    }

    public function index(): View
    {
        $editor = auth()->user();

        // Aktif Sayı: henüz yayında olmayan, en son güncellenen sayı. Editörün elinde
        // birden fazla "hazırlanan" sayı olabilir (taslak+gönderilmiş+onaylanmış vb.) —
        // hangisiyle en son ilgilenmişse o gösterilir.
        $activeIssue = $editor->editedMagazineIssues()
            ->where('status', '!=', ContentStatus::Yayinda)
            ->with('articles')
            ->latest('updated_at')
            ->first();

        $checklist = [];
        $progress = 0;

        if ($activeIssue) {
            $hasArticles = $activeIssue->articles->isNotEmpty();
            // Hiç makale yokken "bekleyen inceleme yok" vakumsal olarak doğru olur (boş
            // kümede her koşul sağlanır) — bu yüzden makale olmadan bu madde işaretlenmez.
            $pendingReview = $hasArticles && $activeIssue->articles->whereIn('status', [ContentStatus::Gonderildi, ContentStatus::Incelemede])->isEmpty();

            $checklist = [
                ['label' => 'Kapak', 'done' => filled($activeIssue->cover_image)],
                ['label' => 'Editör Yazısı', 'done' => filled($activeIssue->editor_note)],
                ['label' => 'Makaleler', 'done' => $hasArticles, 'meta' => $activeIssue->articles->count().' makale'],
                ['label' => 'İnceleme', 'done' => $pendingReview],
                ['label' => 'Onaya Gönderildi', 'done' => in_array($activeIssue->status, [ContentStatus::Gonderildi, ContentStatus::Onaylandi, ContentStatus::Yayinda], true)],
            ];

            $progress = (int) round(collect($checklist)->where('done', true)->count() / count($checklist) * 100);
        }

        $pendingReviewCount = $this->articlesQuery()->whereIn('status', [ContentStatus::Gonderildi, ContentStatus::Incelemede])->count();
        $revisionRequestedCount = $this->articlesQuery()->where('status', ContentStatus::RevizyonIstendi)->count();

        $recentArticles = $this->articlesQuery()->latest('created_at')->take(5)->get();

        $recentIssues = $editor->editedMagazineIssues()->latest('publish_date')->take(5)->get();

        return view('panel.dergi.index', compact(
            'activeIssue', 'checklist', 'progress',
            'pendingReviewCount', 'revisionRequestedCount',
            'recentArticles', 'recentIssues'
        ));
    }

    public function sayilarim(Request $request): View
    {
        $status = ContentStatus::tryFrom((string) $request->query('durum'));

        $issues = auth()->user()->editedMagazineIssues()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('updated_at')
            ->get();

        return view('panel.dergi.sayilarim', compact('issues', 'status'));
    }

    public function makaleHavuzu(Request $request): View
    {
        $tabs = $this->articleTabs();
        $activeTab = $request->query('durum', 'tumu');

        if (! array_key_exists($activeTab, $tabs)) {
            $activeTab = 'tumu';
        }

        $statuses = $tabs[$activeTab]['statuses'];

        $articles = $this->articlesQuery()
            ->when($statuses, fn ($q) => $q->whereIn('status', $statuses))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('panel.dergi.makale-havuzu', compact('articles', 'tabs', 'activeTab'));
    }

    public function yayinTakvimi(): View
    {
        $issues = auth()->user()->editedMagazineIssues()
            ->orderByDesc('publish_date')
            ->orderByDesc('created_at')
            ->get();

        return view('panel.dergi.yayin-takvimi', compact('issues'));
    }
}
