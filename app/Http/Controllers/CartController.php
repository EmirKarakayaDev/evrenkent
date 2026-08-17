<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = auth()->user()->cartItems()->with('book.author')->latest()->get();

        return view('panel.sepetim.index', compact('items'));
    }

    /**
     * Sepete sadece yayındaki kitaplar eklenebilir — zaten satın alınmış bir kitabı
     * tekrar sepete eklemeye gerek yok. Route parametresi {book}, ayrı bir Policy
     * gerekmiyor: sepet her zaman auth()->user()->cartItems() üzerinden scope'lanıyor
     * (favoriler.kitap.toggle / okuma-listesi.kitap.ekle ile aynı desen).
     */
    public function store(Book $book): RedirectResponse
    {
        abort_unless($book->status === ContentStatus::Yayinda, 404);

        $user = auth()->user();

        if ($user->hasPurchased($book)) {
            return back()->with('status', 'Bu kitabı zaten satın aldınız.');
        }

        $user->cartItems()->firstOrCreate(['book_id' => $book->id]);

        return back()->with('status', 'Sepete eklendi.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        auth()->user()->cartItems()->where('book_id', $book->id)->delete();

        return back()->with('status', 'Sepetten çıkarıldı.');
    }

    /**
     * Gerçek ödeme entegrasyonu (Stripe/iyzico) gelecek bir faz — PurchaseController
     * ile aynı mock mantığını kullanan User::purchase() üzerinden sepetteki her
     * (hâlâ yayında olan) kitap için anında/mock tamamlanmış satın alma kaydı
     * oluşturur, ardından sepeti boşaltır.
     */
    public function checkout(): RedirectResponse
    {
        $user = auth()->user();
        $items = $user->cartItems()->with('book')->get();

        foreach ($items as $item) {
            if ($item->book && $item->book->status === ContentStatus::Yayinda) {
                $user->purchase($item->book);
            }
        }

        $user->cartItems()->delete();

        return redirect()->route('panel.satin-aldiklarim')->with('status', 'Satın alma tamamlandı.');
    }
}
