<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Süper Admin'in Kitaplar yönetimi — Filament'teki BookResource'un list/create/edit/
 * delete'inin birebir aynısı (aynı alanlar, aynı policy'ler), kendi panelimizde.
 * BookResource silinmedi/değişmedi — bu, onunla paralel çalışan ikinci bir arayüz.
 */
class AdminBookController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Book::class);

        $books = Book::query()
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.addcslashes($request->string('q'), '%_\\').'%'))
            ->when($request->filled('durum'), fn ($query) => $query->where('status', $request->string('durum')))
            ->with('author')
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('panel.admin.kitaplar.index', [
            'books' => $books,
            'q' => $request->string('q')->toString(),
            'durum' => $request->string('durum')->toString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Book::class);

        return view('panel.admin.kitaplar.form', [
            'book' => null,
            'authors' => User::role('yazar')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Book::class);

        $data = $request->validate($this->validationRules(create: true));

        if ($request->hasFile('cover_image')) {
            // Filament'in FileUpload'ıyla aynı disk/dizin — x-book-cover bileşeni
            // ikisinde de aynı şekilde okuyor.
            $data['cover_image'] = $request->file('cover_image')->store('covers/books', 'public');
        }

        $data['is_editors_pick'] = $request->boolean('is_editors_pick');
        $data['status'] = $data['status'] ?? ContentStatus::Taslak->value;

        $book = Book::create($data);
        $book->categories()->sync($request->input('categories', []));

        return redirect()->route('panel.adminpanel.kitaplar.duzenle', $book)->with('status', 'Kitap oluşturuldu.');
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $book->load('categories');

        return view('panel.admin.kitaplar.form', [
            'book' => $book,
            'authors' => User::role('yazar')->orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $data = $request->validate($this->validationRules(create: false, book: $book));

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers/books', 'public');
        } else {
            unset($data['cover_image']);
        }

        $data['is_editors_pick'] = $request->boolean('is_editors_pick');
        // Durum sadece İçerik Onayları akışıyla (Faz 1) değişir — düzenleme formundan
        // gelen olası bir "status" değeri (yoksa da) burada bilerek yok sayılıyor.
        unset($data['status']);

        $book->update($data);
        $book->categories()->sync($request->input('categories', []));

        return redirect()->route('panel.adminpanel.kitaplar.duzenle', $book)->with('status', 'Kitap güncellendi.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('panel.adminpanel.kitaplar.index')->with('status', 'Kitap silindi.');
    }

    /**
     * @return array<string, array<mixed>>
     */
    private function validationRules(bool $create, ?Book $book = null): array
    {
        return [
            'author_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:255', 'alpha_dash',
                \Illuminate\Validation\Rule::unique('books', 'slug')->ignore($book),
            ],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'status' => $create ? ['required', 'in:'.implode(',', array_column(ContentStatus::cases(), 'value'))] : ['sometimes'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'published_at' => ['nullable', 'date'],
            'average_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            'page_count' => ['nullable', 'integer', 'min:0'],
            'document_count' => ['nullable', 'integer', 'min:0'],
            'video_count' => ['nullable', 'integer', 'min:0'],
            'map_count' => ['nullable', 'integer', 'min:0'],
            'author_note_count' => ['nullable', 'integer', 'min:0'],
            'source_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
