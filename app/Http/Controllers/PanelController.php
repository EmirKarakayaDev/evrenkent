<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Bu alanların gerçek veri modelleri (favoriler, notlar, satın almalar) henüz yok —
     * Faz 2 kapsamında sadece sayfa iskeleti/boş-durum olarak kuruluyor.
     */
    private function placeholder(string $title, string $message): View
    {
        return view('panel.placeholder', compact('title', 'message'));
    }

    public function index(): View
    {
        return $this->placeholder('Kitaplığım', 'Henüz kitaplığınıza eklenmiş bir eser yok.');
    }

    public function favorilerim(): View
    {
        return $this->placeholder('Favorilerim', 'Henüz favorilere eklediğiniz bir eser yok.');
    }

    public function okumaListem(): View
    {
        return $this->placeholder('Okuma Listem', 'Okuma listeniz şu an boş.');
    }

    public function okuduklarim(): View
    {
        return $this->placeholder('Okuduklarım', 'Henüz tamamladığınız bir eser yok.');
    }

    public function defterim(): View
    {
        return $this->placeholder('Defterim', 'Defteriniz şu an boş.');
    }

    public function notlarim(): View
    {
        return $this->placeholder('Notlarım', 'Henüz not almadınız.');
    }

    public function alintilarim(): View
    {
        return $this->placeholder('Alıntılarım', 'Henüz bir alıntı kaydetmediniz.');
    }

    public function satinAldiklarim(): View
    {
        return $this->placeholder('Satın Aldıklarım', 'Henüz bir satın alımınız yok.');
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
