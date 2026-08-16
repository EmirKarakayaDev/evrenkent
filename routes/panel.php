<?php

use App\Http\Controllers\ChapterController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReadingListController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('panel')->as('panel.')->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('index');
    Route::get('/aboneligim', [PanelController::class, 'aboneligim'])->name('aboneligim');
    Route::get('/yardim', [PanelController::class, 'yardim'])->name('yardim');
    Route::get('/iletisim', [PanelController::class, 'iletisim'])->name('iletisim');

    // Bildirimler: header'daki zil ikonu için — tüm girişli kullanıcılar (rol farketmez).
    Route::post('/bildirimler/{notification}/oku', [NotificationController::class, 'read'])->name('bildirimler.oku');
    Route::post('/bildirimler/tumunu-oku', [NotificationController::class, 'readAll'])->name('bildirimler.tumunu-oku');

    Route::get('/favorilerim', [FavoriteController::class, 'index'])->name('favorilerim');
    Route::post('/favoriler/kitap/{book}/toggle', [FavoriteController::class, 'toggleBook'])->name('favoriler.kitap.toggle');
    Route::delete('/favoriler/{favorite}', [FavoriteController::class, 'destroy'])->name('favoriler.sil');

    Route::get('/okuma-listem', [ReadingListController::class, 'okumaListem'])->name('okuma-listem');
    Route::get('/okuduklarim', [ReadingListController::class, 'okuduklarim'])->name('okuduklarim');
    Route::post('/okuma-listesi/kitap/{book}', [ReadingListController::class, 'addBook'])->name('okuma-listesi.kitap.ekle');
    Route::patch('/okuma-listesi/{readingListItem}/tamamla', [ReadingListController::class, 'complete'])->name('okuma-listesi.tamamla');
    Route::patch('/okuma-listesi/{readingListItem}/listeye-al', [ReadingListController::class, 'reopen'])->name('okuma-listesi.listeye-al');
    Route::delete('/okuma-listesi/{readingListItem}', [ReadingListController::class, 'destroy'])->name('okuma-listesi.sil');

    Route::get('/defterim', [NoteController::class, 'defterim'])->name('defterim');
    Route::get('/notlarim', [NoteController::class, 'notlarim'])->name('notlarim');
    Route::get('/alintilarim', [NoteController::class, 'alintilarim'])->name('alintilarim');
    Route::post('/notlar', [NoteController::class, 'store'])->name('notlar.ekle');
    Route::put('/notlar/{note}', [NoteController::class, 'update'])->name('notlar.guncelle');
    Route::delete('/notlar/{note}', [NoteController::class, 'destroy'])->name('notlar.sil');

    Route::get('/satin-aldiklarim', [PurchaseController::class, 'index'])->name('satin-aldiklarim');
    Route::post('/satin-al/{book}', [PurchaseController::class, 'store'])->name('satin-al');

    // Yayın Yönetimi: sadece Yazar rolündeki kullanıcılar erişebilir.
    Route::middleware('role:yazar')->prefix('yayinlarim')->as('yayinlarim.')->group(function () {
        Route::get('/', [PublicationController::class, 'index'])->name('index');
        Route::get('/taslaklarim', [PublicationController::class, 'taslaklarim'])->name('taslaklarim');
        Route::get('/taslaklarim/yeni', [PublicationController::class, 'yeniTaslakForm'])->name('taslaklarim.yeni');
        Route::post('/taslaklarim', [PublicationController::class, 'storeTaslak'])->name('taslaklarim.store');
        Route::get('/gonderilenler', [PublicationController::class, 'gonderilenler'])->name('gonderilenler');
        Route::get('/geri-donenler', [PublicationController::class, 'geriDonenler'])->name('geri-donenler');
        Route::get('/yayinlananlar', [PublicationController::class, 'yayinlananlar'])->name('yayinlananlar');
        Route::get('/istatistiklerim', [PublicationController::class, 'istatistiklerim'])->name('istatistiklerim');
        Route::post('/kitap/{book}/gonder', [PublicationController::class, 'submitBook'])->name('kitap.gonder');
        Route::post('/makale/{article}/gonder', [PublicationController::class, 'submitArticle'])->name('makale.gonder');
        Route::get('/kitap/{book}/duzenle', [PublicationController::class, 'editBook'])->name('kitap.duzenle');
        Route::put('/kitap/{book}', [PublicationController::class, 'updateBook'])->name('kitap.guncelle');
        Route::get('/makale/{article}/duzenle', [PublicationController::class, 'editArticle'])->name('makale.duzenle');
        Route::put('/makale/{article}', [PublicationController::class, 'updateArticle'])->name('makale.guncelle');

        Route::get('/kitap/{book}/bolumler', [ChapterController::class, 'index'])->name('kitap.bolumler');
        Route::get('/kitap/{book}/bolumler/yeni', [ChapterController::class, 'create'])->name('kitap.bolumler.yeni');
        Route::post('/kitap/{book}/bolumler', [ChapterController::class, 'store'])->name('kitap.bolumler.store');
        Route::get('/kitap/{book}/bolumler/{chapter}/duzenle', [ChapterController::class, 'edit'])->name('kitap.bolumler.duzenle');
        Route::put('/kitap/{book}/bolumler/{chapter}', [ChapterController::class, 'update'])->name('kitap.bolumler.guncelle');
        Route::delete('/kitap/{book}/bolumler/{chapter}', [ChapterController::class, 'destroy'])->name('kitap.bolumler.sil');
    });
});
