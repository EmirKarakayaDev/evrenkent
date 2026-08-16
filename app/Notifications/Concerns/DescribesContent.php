<?php

namespace App\Notifications\Concerns;

use App\Filament\Resources\MagazineIssueResource;
use App\Models\Article;
use App\Models\Book;
use App\Models\MagazineIssue;
use Illuminate\Database\Eloquent\Model;

/**
 * Book/Article/MagazineIssue durum bildirimlerinin ortak kısmı: içerik türünün
 * etiketi ("Kitabınız"/"Makaleniz"/"Sayınız") ve alıcının gidebileceği bağlantı.
 */
trait DescribesContent
{
    protected function contentLabel(Model $content): string
    {
        return match (true) {
            $content instanceof Book => 'Kitabınız',
            $content instanceof Article => 'Makaleniz',
            $content instanceof MagazineIssue => 'Sayınız',
            default => 'İçeriğiniz',
        };
    }

    protected function contentUrl(Model $content): string
    {
        return match (true) {
            $content instanceof Book => route('panel.yayinlarim.kitap.duzenle', $content),
            $content instanceof Article => route('panel.yayinlarim.makale.duzenle', $content),
            $content instanceof MagazineIssue => MagazineIssueResource::getUrl('edit', ['record' => $content]),
            default => route('home'),
        };
    }
}
