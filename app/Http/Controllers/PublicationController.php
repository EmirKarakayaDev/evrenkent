<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Article;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function index(): View
    {
        return $this->listView('Yayınlarım', null, 'panel.yayinlarim.index');
    }

    public function taslaklarim(): View
    {
        return $this->listView('Taslaklarım', ContentStatus::Taslak, 'panel.yayinlarim.taslaklarim', showActions: true);
    }

    public function gonderilenler(): View
    {
        return $this->listView(
            'Gönderilenler',
            [ContentStatus::Gonderildi, ContentStatus::Incelemede],
            'panel.yayinlarim.gonderilenler'
        );
    }

    public function geriDonenler(): View
    {
        return $this->listView('Geri Dönenler', ContentStatus::RevizyonIstendi, 'panel.yayinlarim.geri-donenler', showActions: true);
    }

    public function yayinlananlar(): View
    {
        return $this->listView('Yayınlananlar', ContentStatus::Yayinda, 'panel.yayinlarim.yayinlananlar');
    }

    public function istatistiklerim(): View
    {
        return view('panel.placeholder', [
            'title' => 'İstatistiklerim',
            'message' => 'Yayın istatistikleri (okunma, satış, gelir) yakında burada olacak.',
        ]);
    }

    public function yeniTaslakForm(): View
    {
        return view('panel.yayinlarim.yeni');
    }

    public function editBook(Book $book): View
    {
        $this->authorize('update', $book);

        return view('panel.yayinlarim.kitap-duzenle', compact('book'));
    }

    public function updateBook(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $book->update([
            'title' => $data['title'],
            'description' => $data['body'],
            'price' => $data['price'] ?? 0,
        ]);

        return redirect()->route($this->listRouteFor($book->status))->with('status', 'Kitap güncellendi.');
    }

    public function editArticle(Article $article): View
    {
        $this->authorize('update', $article);

        return view('panel.yayinlarim.makale-duzenle', compact('article'));
    }

    public function updateArticle(Request $request, Article $article): RedirectResponse
    {
        $this->authorize('update', $article);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $article->update([
            'title' => $data['title'],
            'content' => $data['body'],
        ]);

        return redirect()->route($this->listRouteFor($article->status))->with('status', 'Makale güncellendi.');
    }

    /**
     * Düzenleme sonrası hangi listeye dönüleceğini kaydın durumuna göre belirler
     * (Taslak -> Taslaklarım, Revizyon İstendi -> Geri Dönenler).
     */
    private function listRouteFor(ContentStatus $status): string
    {
        return $status === ContentStatus::RevizyonIstendi
            ? 'panel.yayinlarim.geri-donenler'
            : 'panel.yayinlarim.taslaklarim';
    }

    public function storeTaslak(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:kitap,makale'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = $request->user();
        $slug = Str::slug($data['title']).'-'.Str::random(6);

        if ($data['type'] === 'kitap') {
            $this->authorize('create', Book::class);

            Book::create([
                'author_id' => $user->id,
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['body'],
                'price' => $data['price'] ?? 0,
                'status' => ContentStatus::Taslak,
            ]);
        } else {
            $this->authorize('create', Article::class);

            Article::create([
                'author_id' => $user->id,
                'title' => $data['title'],
                'slug' => $slug,
                'content' => $data['body'],
                'status' => ContentStatus::Taslak,
            ]);
        }

        return redirect()->route('panel.yayinlarim.taslaklarim')->with('status', 'Taslak oluşturuldu.');
    }

    public function submitBook(Book $book): RedirectResponse
    {
        $this->authorize('submit', $book);

        $book->update(['status' => ContentStatus::Gonderildi]);

        $book->reviews()->create([
            'reviewer_id' => auth()->id(),
            'action' => 'gonderildi',
            'note' => 'Yazar tarafından Süper Admin onayına gönderildi.',
        ]);

        return back()->with('status', 'Kitap onaya gönderildi.');
    }

    public function submitArticle(Article $article): RedirectResponse
    {
        $this->authorize('submit', $article);

        $article->update(['status' => ContentStatus::Gonderildi]);

        $article->reviews()->create([
            'reviewer_id' => auth()->id(),
            'action' => 'gonderildi',
            'note' => $article->magazine_issue_id
                ? 'Yazar tarafından Dergi Editörüne gönderildi.'
                : 'Yazar tarafından gönderildi.',
        ]);

        return back()->with('status', 'Makale gönderildi.');
    }

    /**
     * @param  ContentStatus|array<ContentStatus>|null  $status
     */
    private function listView(string $title, ContentStatus|array|null $status, string $view, bool $showActions = false): View
    {
        $user = auth()->user();

        $books = $user->books()
            ->with(['reviews' => fn ($query) => $query->latest()])
            ->when($status, fn ($query) => $query->whereIn('status', is_array($status) ? $status : [$status]))
            ->latest()
            ->get();

        $articles = $user->articles()
            ->with(['reviews' => fn ($query) => $query->latest()])
            ->when($status, fn ($query) => $query->whereIn('status', is_array($status) ? $status : [$status]))
            ->latest()
            ->get();

        return view($view, compact('title', 'books', 'articles', 'showActions'));
    }
}
