<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Bu alanların (Aboneliğim, Yardım, İletişim) henüz gerçek bir veri modeli yok —
     * sadece sayfa iskeleti/boş-durum olarak kuruluyor.
     */
    private function placeholder(string $title, string $message): View
    {
        return view('panel.placeholder', compact('title', 'message'));
    }

    public function index(): View
    {
        return $this->placeholder('Kitaplığım', 'Henüz kitaplığınıza eklenmiş bir eser yok.');
    }

    public function aboneligim(): View
    {
        return $this->placeholder('Aboneliğim', auth()->user()->is_premium
            ? 'Premium aboneliğiniz aktif.'
            : 'Şu an ücretsiz hesap kullanıyorsunuz.');
    }

    public function yardim(): View
    {
        return $this->placeholder('Yardım Merkezi', 'Yardım içerikleri yakında burada olacak.');
    }

    public function iletisim(): View
    {
        return $this->placeholder('İletişim', 'İletişim formu yakında eklenecek.');
    }
}
