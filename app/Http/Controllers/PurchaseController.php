<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = auth()->user()->purchases()
            ->with('book.author')
            ->latest('purchased_at')
            ->get();

        return view('panel.satin-aldiklarim.index', compact('purchases'));
    }

    /**
     * Gerçek ödeme entegrasyonu (Stripe/iyzico) gelecek bir faz —
     * bu metod ödeme sorulmadan anında/mock tamamlanmış bir satın alma kaydı oluşturur.
     */
    public function store(Book $book): RedirectResponse
    {
        abort_unless($book->status === ContentStatus::Yayinda, 404);

        $user = auth()->user();

        $user->purchases()->firstOrCreate([
            'book_id' => $book->id,
        ], [
            'amount' => $book->price,
            'purchased_at' => now(),
            'payment_status' => 'completed',
        ]);

        return back()->with('status', 'Satın alma tamamlandı.');
    }
}
