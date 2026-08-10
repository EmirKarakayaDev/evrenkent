<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\NoteType;
use App\Enums\ReadingStatus;
use App\Models\Article;
use App\Models\Book;
use App\Models\Category;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Deneme/demo amaçlı gerçekçi içerik üretir (kitap, makale, dergi sayısı, kategori,
 * ve Okur'a ait favoriler/okuma listesi/notlar/satın almalar). Her rolün panelinde
 * her durum/bölüm en az bir kayıtla temsil edilir. Ana seed zincirine dahil değildir
 * — ayrıca çalıştırılır:
 *   php artisan db:seed --class=DemoContentSeeder
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect(['Roman', 'Şiir', 'Tarih', 'Deneme', 'Bilim Kurgu'])
            ->mapWithKeys(fn (string $name) => [
                $name => Category::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name]
                ),
            ]);

        $author1 = User::firstOrCreate(
            ['email' => 'author@evrenkent.test'],
            ['name' => 'Yazar', 'password' => bcrypt('password')]
        );
        if (! $author1->hasRole('yazar')) {
            $author1->assignRole('yazar');
        }

        $author2 = User::firstOrCreate(
            ['email' => 'elif.nazli@evrenkent.test'],
            ['name' => 'Elif Nazlı', 'password' => bcrypt('password')]
        );
        if (! $author2->hasRole('yazar')) {
            $author2->assignRole('yazar');
        }

        $editor = User::firstOrCreate(
            ['email' => 'editor@evrenkent.test'],
            ['name' => 'Dergi Editörü', 'password' => bcrypt('password')]
        );
        if (! $editor->hasRole('dergi_editoru')) {
            $editor->assignRole('dergi_editoru');
        }

        $admin = User::where('email', 'admin@evrenkent.test')->first();
        $reader = User::where('email', 'reader@evrenkent.test')->first();

        // --- Kitaplar ---
        $books = [
            ['title' => 'Sislerin Ardındaki Fener', 'author' => $author2, 'category' => 'Roman', 'price' => 189, 'status' => ContentStatus::Yayinda],
            ['title' => 'Kökler ve Kanatlar', 'author' => $author2, 'category' => 'Şiir', 'price' => 120, 'status' => ContentStatus::Yayinda],
            ['title' => 'Zamanın İzinde', 'author' => $author1, 'category' => 'Tarih', 'price' => 245, 'status' => ContentStatus::Gonderildi],
            ['title' => 'Dinginliğin Kıyısında', 'author' => $author1, 'category' => 'Deneme', 'price' => 150, 'status' => ContentStatus::Taslak],
            ['title' => 'Medeniyetin Ayak İzleri', 'author' => $author2, 'category' => 'Tarih', 'price' => 210, 'status' => ContentStatus::RevizyonIstendi],
            ['title' => 'Uzak Sahiller', 'author' => $author2, 'category' => 'Bilim Kurgu', 'price' => 175, 'status' => ContentStatus::Onaylandi],
            ['title' => 'Kayıp Zamanın Şiirleri', 'author' => $author1, 'category' => 'Şiir', 'price' => 95, 'status' => ContentStatus::RevizyonIstendi],
        ];

        $bookModels = [];
        foreach ($books as $data) {
            $book = Book::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'author_id' => $data['author']->id,
                    'title' => $data['title'],
                    'description' => fake()->paragraphs(3, true),
                    'price' => $data['price'],
                    'status' => $data['status'],
                    'published_at' => $data['status'] === ContentStatus::Yayinda ? now()->subDays(random_int(1, 60)) : null,
                ]
            );

            $bookModels[$data['title']] = $book;

            $book->categories()->syncWithoutDetaching([$categories[$data['category']]->id]);

            if ($data['status'] === ContentStatus::RevizyonIstendi && $admin) {
                $book->reviews()->firstOrCreate([
                    'reviewer_id' => $admin->id,
                    'action' => 'revizyon_istendi',
                ], [
                    'note' => $data['title'] === 'Kayıp Zamanın Şiirleri'
                        ? 'Şiir başlıkları eksik, lütfen tamamlayın.'
                        : 'Kapak görseli eksik, lütfen ekleyip tekrar gönderin.',
                ]);
            }

            if ($data['status'] === ContentStatus::Gonderildi && $admin) {
                $book->reviews()->firstOrCreate([
                    'reviewer_id' => $author1->id,
                    'action' => 'gonderildi',
                ], [
                    'note' => 'Yazar tarafından Süper Admin onayına gönderildi.',
                ]);
            }
        }

        // --- Dergi Sayıları ---
        $issues = [
            ['title' => 'Bilim Tarihi Dergisi - Sayı 23', 'number' => 23, 'status' => ContentStatus::Yayinda],
            ['title' => 'Astronomi Dergisi - Sayı 15', 'number' => 15, 'status' => ContentStatus::Yayinda],
            ['title' => 'Felsefe Dergisi - Sayı 10', 'number' => 10, 'status' => ContentStatus::Gonderildi],
            ['title' => 'Edebiyat Dergisi - Sayı 8', 'number' => 8, 'status' => ContentStatus::Taslak],
            ['title' => 'Sanat Tarihi Dergisi - Sayı 5', 'number' => 5, 'status' => ContentStatus::Onaylandi],
            ['title' => 'Matematik Dergisi - Sayı 12', 'number' => 12, 'status' => ContentStatus::RevizyonIstendi],
        ];

        $issueModels = [];
        foreach ($issues as $data) {
            $issueModels[$data['title']] = MagazineIssue::firstOrCreate(
                ['title' => $data['title']],
                [
                    'editor_id' => $editor->id,
                    'issue_number' => $data['number'],
                    'status' => $data['status'],
                    'publish_date' => $data['status'] === ContentStatus::Yayinda ? now()->subDays(random_int(1, 90)) : null,
                ]
            );

            if ($data['status'] === ContentStatus::RevizyonIstendi && $admin) {
                $issueModels[$data['title']]->reviews()->firstOrCreate([
                    'reviewer_id' => $admin->id,
                    'action' => 'revizyon_istendi',
                ], [
                    'note' => 'Kapak görseli ve içindekiler eksik, lütfen tamamlayın.',
                ]);
            }
        }

        // --- Makaleler ---
        $articles = [
            ['title' => 'Evrenin Yaşı ve Genişlemesi', 'author' => $author1, 'issue' => 'Astronomi Dergisi - Sayı 15', 'category' => 'Bilim Kurgu', 'status' => ContentStatus::Yayinda],
            ['title' => 'Kara Deliklerin Sırları', 'author' => $author2, 'issue' => 'Astronomi Dergisi - Sayı 15', 'category' => 'Bilim Kurgu', 'status' => ContentStatus::Onaylandi],
            ['title' => 'Galileo\'nun Gözlemleri', 'author' => $author1, 'issue' => 'Bilim Tarihi Dergisi - Sayı 23', 'category' => 'Tarih', 'status' => ContentStatus::Yayinda],
            ['title' => 'Yıldızların Yaşam Döngüsü', 'author' => $author2, 'issue' => 'Felsefe Dergisi - Sayı 10', 'category' => 'Bilim Kurgu', 'status' => ContentStatus::Incelemede],
            ['title' => 'Kozmik Işınlar ve Dünya', 'author' => $author1, 'issue' => null, 'category' => 'Deneme', 'status' => ContentStatus::Taslak],
            ['title' => 'Kuantum Hesaplama Temelleri', 'author' => $author1, 'issue' => 'Felsefe Dergisi - Sayı 10', 'category' => 'Bilim Kurgu', 'status' => ContentStatus::Gonderildi],
            ['title' => 'Kayıp Uygarlıklar', 'author' => $author2, 'issue' => 'Bilim Tarihi Dergisi - Sayı 23', 'category' => 'Tarih', 'status' => ContentStatus::RevizyonIstendi],
        ];

        foreach ($articles as $data) {
            $article = Article::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'author_id' => $data['author']->id,
                    'magazine_issue_id' => $data['issue'] ? $issueModels[$data['issue']]->id : null,
                    'title' => $data['title'],
                    'content' => fake()->paragraphs(4, true),
                    'status' => $data['status'],
                    'published_at' => $data['status'] === ContentStatus::Yayinda ? now()->subDays(random_int(1, 60)) : null,
                ]
            );

            $article->categories()->syncWithoutDetaching([$categories[$data['category']]->id]);

            if ($data['status'] === ContentStatus::Gonderildi) {
                $article->reviews()->firstOrCreate([
                    'reviewer_id' => $data['author']->id,
                    'action' => 'gonderildi',
                ], [
                    'note' => 'Yazar tarafından Dergi Editörüne gönderildi.',
                ]);
            }

            if ($data['status'] === ContentStatus::RevizyonIstendi && $admin) {
                $article->reviews()->firstOrCreate([
                    'reviewer_id' => $admin->id,
                    'action' => 'revizyon_istendi',
                ], [
                    'note' => 'Kaynakça eksik, lütfen ekleyin.',
                ]);
            }
        }

        // --- Okur: Favoriler / Okuma Listesi / Notlar / Satın Alımlar ---
        if ($reader) {
            $sisler = $bookModels['Sislerin Ardındaki Fener'];
            $kokler = $bookModels['Kökler ve Kanatlar'];

            $reader->favorites()->firstOrCreate([
                'favoritable_type' => Book::class,
                'favoritable_id' => $sisler->id,
            ]);
            $reader->favorites()->firstOrCreate([
                'favoritable_type' => Book::class,
                'favoritable_id' => $kokler->id,
            ]);

            $reader->readingListItems()->firstOrCreate([
                'readable_type' => Book::class,
                'readable_id' => $sisler->id,
            ], [
                'status' => ReadingStatus::Listede,
            ]);
            $reader->readingListItems()->firstOrCreate([
                'readable_type' => Book::class,
                'readable_id' => $kokler->id,
            ], [
                'status' => ReadingStatus::Tamamlandi,
                'completed_at' => now()->subDays(5),
            ]);

            $reader->notes()->firstOrCreate([
                'type' => NoteType::Defter,
                'content' => 'Bugün güzel bir gün, yeni bir kitaba başlamalıyım.',
            ]);
            $reader->notes()->firstOrCreate([
                'type' => NoteType::Not,
                'noteable_type' => Book::class,
                'noteable_id' => $sisler->id,
            ], [
                'content' => 'Bu kitaptaki karakter gelişimi çok başarılı.',
            ]);
            $reader->notes()->firstOrCreate([
                'type' => NoteType::Alinti,
                'noteable_type' => Book::class,
                'noteable_id' => $kokler->id,
            ], [
                'content' => 'Kökleri olmayan kanatlar, sadece rüzgarda savrulur.',
                'location' => 'Sayfa 12',
            ]);

            $reader->purchases()->firstOrCreate([
                'book_id' => $sisler->id,
            ], [
                'amount' => $sisler->price,
                'purchased_at' => now()->subDays(3),
                'payment_status' => 'completed',
            ]);
        }

        $this->command?->info('Demo içerik oluşturuldu: '.count($categories).' kategori, '.count($books).' kitap, '.count($issues).' dergi sayısı, '.count($articles).' makale.');
    }
}
