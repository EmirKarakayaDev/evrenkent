<?php

use App\Http\Controllers\PanelController;
use App\Http\Controllers\PublicationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('panel')->as('panel.')->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('index');
    Route::get('/favorilerim', [PanelController::class, 'favorilerim'])->name('favorilerim');
    Route::get('/okuma-listem', [PanelController::class, 'okumaListem'])->name('okuma-listem');
    Route::get('/okuduklarim', [PanelController::class, 'okuduklarim'])->name('okuduklarim');
    Route::get('/defterim', [PanelController::class, 'defterim'])->name('defterim');
    Route::get('/notlarim', [PanelController::class, 'notlarim'])->name('notlarim');
    Route::get('/alintilarim', [PanelController::class, 'alintilarim'])->name('alintilarim');
    Route::get('/satin-aldiklarim', [PanelController::class, 'satinAldiklarim'])->name('satin-aldiklarim');
    Route::get('/aboneligim', [PanelController::class, 'aboneligim'])->name('aboneligim');
    Route::get('/yardim', [PanelController::class, 'yardim'])->name('yardim');
    Route::get('/iletisim', [PanelController::class, 'iletisim'])->name('iletisim');

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
    });
});
