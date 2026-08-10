# Evrenkent — Canlıya Alma Öncesi Kontrol Listesi

Bu proje şu an **yerel geliştirme ortamı** için yapılandırılmıştır. Gerçek bir sunucuya (canlı ortama) taşınmadan önce aşağıdaki maddeler mutlaka ele alınmalıdır. Bunlar kod değişikliği değil, ortam/konfigürasyon işleridir.

## 🔴 Kritik (mutlaka yapılmalı)

- [ ] **`APP_DEBUG=false` yap.** Açık kalırsa herhangi bir hatada ziyaretçiye tam stack trace, `.env` değişkenleri ve veritabanı sorguları gösterilir (Ignition hata sayfası) — ciddi bir bilgi sızıntısı riski.
- [ ] **`APP_ENV=production` yap.**
- [ ] **Gerçek bir e-posta servisi bağla** (`MAIL_MAILER`, SMTP bilgileri — SendGrid, Postmark, Mailgun vb.). Şu an `log` sürücüsü kullanılıyor, yani şifre sıfırlama/doğrulama e-postaları **hiç gönderilmiyor**, sadece log dosyasına yazılıyor.
- [ ] **`APP_URL`'i gerçek domain'e güncelle.** Şifre sıfırlama linkleri ve diğer imzalı URL'ler bu değere göre üretiliyor.
- [ ] **Seed edilen tüm hesapların şifresini değiştir veya hesapları sil.** (`admin@evrenkent.test`, `editor@evrenkent.test`, `author@evrenkent.test`, `reader@evrenkent.test` — hepsi `password` şifresiyle oluşturuldu, sadece geliştirme içindir.)
- [ ] **`DemoContentSeeder`'ı canlıda asla çalıştırma.** Sahte kullanıcı/kitap/makale/dergi verisi oluşturur, sadece demo amaçlıdır.
- [ ] **`php artisan migrate:fresh` gibi yıkıcı komutları canlı veritabanında asla çalıştırma.**

## 🟠 Önemli

- [ ] **`SESSION_SECURE_COOKIE=true` yap** (site HTTPS üzerinden çalışacaksa — ki çalışmalı).
- [ ] **`php artisan storage:link` çalıştır** (kapak görsellerinin görünmesi için — sembolik link `.gitignore`'da olduğundan repoya taşınmaz, her ortamda ayrıca oluşturulmalı).
- [ ] **Gerçek bir veritabanına geç** (şu an SQLite kullanılıyor — MySQL/PostgreSQL gibi bir üretim veritabanına geçiş `.env`'de `DB_CONNECTION` değiştirilerek yapılabilir).
- [ ] **`php artisan config:cache`, `route:cache`, `view:cache` çalıştır** (performans için).
- [ ] **Kuyruk çalıştırıcısını (queue worker) kur** — `QUEUE_CONNECTION=database` kullanılıyor, ileride e-posta/bildirim gibi kuyruklu işler eklenirse `php artisan queue:work` bir process manager (Supervisor vb.) ile sürekli çalışır durumda olmalı.

## 🟡 Küçük / Gözden Geçirilmeli

- [ ] 404/500 hata sayfaları hâlâ Laravel varsayılanı — tasarım sistemine uyacak şekilde özelleştirilebilir.
- [ ] Favicon eklenmedi.
- [ ] `BookResource`/`MagazineIssueResource`'daki Yazar/Editör seçim kutuları tüm kullanıcıları listeliyor (role'e göre filtrelenmemiş) — veri girişinde kafa karıştırabilir, güvenlik açığı değil.
- [ ] E-posta doğrulama (`MustVerifyEmail`) şu an hiçbir yerde zorunlu kılınmıyor — Breeze'in doğrulama akışı kurulu ama devre dışı, istenirse `User` modeline `implements MustVerifyEmail` eklenip ilgili route'lara `verified` middleware'i eklenerek etkinleştirilebilir.

## ✅ Zaten Kontrol Edildi, Sorun Yok

- `composer audit` ve `npm audit` — bilinen güvenlik açığı yok.
- Tüm Blade view'ları `{{ }}` ile otomatik escape ediyor, kaçışsız (`{!! !!}`) çıktı hiçbir yerde kullanılmıyor — XSS riski yok.
- CSRF koruması tüm formlarda aktif (Laravel varsayılanı).
- Kategori/kitap/makale ilişkilerinde `cascadeOnDelete` doğru kurulu, orphan veri riski yok.
- Filament admin girişinde yerleşik rate limiting var.
- Kapak görseli yüklemelerinde dosya boyutu sınırı var (`->maxSize(5120)`, 5MB).
