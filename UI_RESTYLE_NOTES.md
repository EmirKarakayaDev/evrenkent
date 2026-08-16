# Evrenkent — UI Restyle Notları

Bu dosya, `dosyalar/` klasöründeki mockup'lara görsel yakınsama çalışmasının
neden/nasıl/ne zaman kayıtlarını tutar. Amaç: ileride "bu neden böyle
yapılmış" ya da "bunu değiştirmiş miydik" karışıklığını önlemek.

Fonksiyonel/işlevsel durum (route/controller/policy/test) bu dosyanın konusu
**değil** — o taraf zaten sağlam ve ayrıca doğrulandı (82 test geçiyor).
Burada sadece **görsel/tasarım** kararları ve gerekçeleri var.

## Kapsam dışı bırakılanlar (bilinçli, sonraya ertelendi)

Bunlara bu faz içinde **dokunmuyoruz** — karıştırmayalım:

- **Kitap kapak görselleri** (`x-book-cover` bileşeni, gerçek illüstrasyon/upload) — placeholder gradyan aynen kalıyor.
- **Okuma modu sayfası** (`books/read.blade.php`) — şu an sade, mockup'taki "kitap sayfası" hissi (kağıt dokusu, gömülü belge/video) sonraya.
- **Dergi Editörü / Süper Admin panelleri** — şu an generic Filament admin arayüzü kullanıyorlar; mockup'taki özel dashboard'lar (grafik, sayı oluşturucu sihirbazı vb.) ayrı ve büyük bir iş, sonraya.

## Bilinçli olarak mockup'tan FARKLI bırakılanlar (ve nedeni)

- **Header'daki arama ikonu ve sepet ikonu sadece görsel.** Gerçek arama
  sorgusu ve sepet (çoklu ürün / tek seferde satın alma) backend'i yok.
  `title="Yakında"` ile üstüne gelince ipucu veriyoruz, kullanıcıyı
  yanıltmasın diye. **Kullanıcı onayıyla** bilinçli bırakıldı (2026-08-13).
  İleride ayrı bir faz olarak ele alınacak. (Not: kullanıcı geri bildirimiyle
  arama artık mockup'taki gibi sadece ikon — metin kutusu değil; sepette
  "Sepetim" yazısı + ikon var ama sahte "2" gibi bir sayaç yok.)
- **Hamburger menü (☰), girişli kullanıcı için gerçek işlevsel** —
  `auth()->user()->redirectPath()`'e gidiyor, "Panelim" text linkinin
  yerini aldı. Ziyaretçi için henüz bir mega-menü/kategori gezinme paneli
  yok, o yüzden ziyaretçide görsel/pasif (`title="Yakında"`) kalıyor —
  bu da ileride ayrı bir iş.
- **Anasayfadaki "Çok Satanlar / Editörün Seçkisi / Fırsatlar / Yakında
  Çıkacaklar" sekmeleri pasif/tıklanamaz** (soluk renkte, `title="Yakında"`).
  Bu verileri besleyecek bir model/sıralama mantığı henüz yok; sahte içerik
  göstermek yerine dürüst bir "yakında" hali tercih edildi. Sadece "Yeni
  Çıkanlar" gerçek veriyle çalışıyor (zaten `HomeController` bunu üretiyor).
- **"Sepetim 2" gibi sahte sayaçlar eklenmedi.** Mockup'ta sepette 2 ürün
  gösteriliyor ama bu gerçek bir veri değil — sahte sayı göstermek yanıltıcı
  olur, o yüzden sepet ikonu sayaçsız.
- **Kitap Tanıtım Sayfası'nda yıldız puanı / değerlendirme sayısı / sayfa-
  belge-video-harita sayaç şeridi yok.** Mockup'ta var ama projede hiç bir
  yorum/puanlama (review/rating) sistemi kurulmadı — bu veriyi uydurmak
  (örn. "4.8 yıldız, 128 değerlendirme") kullanıcıyı yanıltır. Onun yerine
  **gerçek veri**yle dolduruldu: kategori etiketleri (`Book::categories`),
  bölüm sayısı (`chapters()->count()`), yayın tarihi. Yorum/puanlama sistemi
  ayrı bir özellik olarak eklenirse bu şerit genişletilebilir.

## İlerleme sırası ve durum

| # | Alan | Durum | Ne yapıldı |
|---|------|-------|------------|
| 1 | Tasarım sistemi (renk/kart/buton) | ✅ Tamamlandı | `tailwind.config.js`'e `brand` turuncu skala; `app.css`'e `.btn-brand/.btn-dark/.btn-outline`, `.card/.card-hover`, `.pill-active/.pill-idle` sınıfları |
| 2 | Header/nav | ✅ Tamamlandı (4 tur kullanıcı geri bildirimiyle revize edildi) | `layouts/public.blade.php` — solda hamburger ikonu, arama artık sadece ikon (kutu değil), sepet ikon+"Sepetim" yazısı (sayaç yok — sahte veri), "Kayıt Ol" butonu `.btn-dark`. **2. revizyon:** Hamburger sayfa değiştirmiyor — girişli kullanıcıda Alpine.js ile soldan açılan bir panel menüsü açıyor. Panel menüsü tekrarını önlemek için `components/panel-nav.blade.php` bileşenine çıkarıldı, hem `layouts/panel.blade.php` hem bu menü aynı bileşeni kullanıyor. **3. revizyon:** İlk halde menü koyu bir perde (backdrop) ile sayfanın üstüne overlay olarak açılıyordu — kullanıcı bunun yerine YouTube tarzı istedi: artık overlay değil, normal doküman akışında; açılınca header hariç içerik alanı (`main`+`footer`) gerçekten daralıyor/itiliyor, backdrop yok, sidebar sticky olarak scroll'da sabit kalıyor. Ziyaretçide hamburger hâlâ görsel/pasif (mega-menü yok). **4. revizyon (2026-08-14):** Üç değişiklik birden: (a) Ziyaretçide hamburger artık hiç render edilmiyor — pasif/soluk ikon yerine tamamen kaldırıldı (mega-menüsü olmayan bir ikonu göstermenin anlamı yoktu). (b) `User::redirectPath()` — okur girişten sonra artık `/panel`'e değil `/`'e (anasayfa) düşüyor, sidebar zaten açık geliyor. (c) **Gerçek SPA-hissi:** `layouts/panel.blade.php` kendi header/sidebar'ını kurmayı bıraktı, `@extends('layouts.public')`'a indirgendi — artık panel sayfaları da aynı ortak kabuğu kullanıyor. `@hotwired/turbo` eklendi (`resources/js/app.js`) — artık sidebar'daki (veya sitedeki herhangi bir) linke tıklamak tam sayfa yenilemesi yapmıyor, Turbo Drive `<body>`'yi fetch ile değiştirip sadece `<main>` içeriğini günceliyor, header/sidebar DOM'da kalıyor (YouTube'daki gibi). Sidebar açık/kapalı durumu artık `Alpine.store('ui').sidebarOpen`'da tutuluyor (`x-data` yerine) — Turbo `<body>`'yi değiştirse de JS ortamı (ve dolayısıyla store) sayfalar arası canlı kaldığı için sidebar durumu geçişler arasında kaybolmuyor. Aktif link vurgulaması (`request()->routeIs()`) her Turbo geçişinde sunucudan taze geldiği için sidebar `data-turbo-permanent` **değil** — bilerek, aksi halde eski sayfanın vurgusu donardı. |
| 3 | Anasayfa | ✅ Tamamlandı (3 tur kullanıcı geri bildirimiyle revize edildi) | `home.blade.php` — kategori sekmeleri (pill), kitap kartları `.card-hover`. "Kitaplar" kutusu ve aktif "Yeni Çıkanlar" pill'i arka plan rengi değiştirmiyor (sadece kenarlık/metin), kutu ikonları daire arka plandan çıkarıldı (düz ikon). **2. revizyon:** border-radius `rounded-2xl` fazla geldi — `.card`/`.card-hover` ve stat kutuları `rounded-lg`'ye küçültülüp eşitlendi. **3. revizyon:** pill'ler (`Yeni Çıkanlar/Çok Satanlar/...`) artık `.card`/stat kutularıyla aynı `rounded-lg` + renk mantığını kullanıyor (`pill` sınıfı `rounded-full`'den `rounded-lg`'ye değişti; idle pilller stat kutularındaki gibi `text-slate-400`). Sayfa üstündeki "Okumanın yeni bir evreni / Kitaplar, dergiler ve sözlükler — tek yerde" başlık bloğu kaldırıldı (gereksiz görüldü). Kitap sayfasındaki gerçek kategori etiketleri (`Roman` vb.) artık ayrı bir `.pill-tag` sınıfı kullanıyor — `pill-idle`'ın "yakında/pasif" soluk rengini miras almasın diye. |
| 4 | Kitap Tanıtım Sayfası | ✅ Tamamlandı | `books/show.blade.php` — sağda sticky "satın alma kartı" (fiyat + CTA yığını), gerçek kategori etiketleri, gerçek bölüm sayısı+yayın tarihi, gerçek verili "Bu Eserler de Dikkatini Çekebilir" şeridi. `BookController::show()` — `categories` eager-load, `chapterCount`, `relatedBooks` (önce kategori eşleşmesi, yetmezse yazarın diğer kitaplarıyla tamamlanır) eklendi |
| 5 | Kitaplığım | ✅ Tamamlandı | `panel/kitapligim.blade.php` — veri tarafı (`bea0f2c`) ayrı bir işti; bu fazda görsel olarak tasarım sistemine geçirildi: ham `bg-white border rounded-lg` yerine `.card`, kategori etiketi `.pill-tag` (küçük varyant, `show.blade.php` ile aynı), aksiyon butonları `.btn-brand`/`.btn-outline` `btn-sm`, "Favori" rozeti sabit `orange-*` yerine `brand-*` skalasına geçti. |
| 6 | Panelin geri kalanı (Favorilerim, Okuma Listem/Okuduklarım, Defterim/Notlarım/Alıntılarım, Satın Aldıklarım, Yazar'ın Yayınlarım listesi) | ✅ Tamamlandı | Hepsi Kitaplığım'da kurulan kalıba geçirildi: ham `bg-white border border-slate-200 rounded-lg` → `.card`, elle yazılmış buton class'ları → `.btn-outline`/`.btn-dark` (+ `.btn-sm` liste satırlarında), sabit `text-orange-700` içerik-türü etiketleri → `.pill-tag` mantığıyla aynı `text-brand-700` (marka rengiyle tutarlı olsun diye). Değişen dosyalar: `panel/favorilerim/_liste.blade.php`, `panel/okuma-listesi/_liste.blade.php`, `panel/notlar/_liste.blade.php`, `panel/notlar/_form.blade.php` (form kartı + "Kaydet" butonu), `panel/satin-aldiklarim/index.blade.php`, `panel/yayinlarim/_liste.blade.php`, `panel/yayinlarim/taslaklarim.blade.php` ("Yeni Taslak Oluştur" butonu), `panel/placeholder.blade.php`. **Kapsam dışı bırakıldı:** yazarın kitap/makale/bölüm **düzenleme-oluşturma formları** (`kitap-duzenle`, `kitap-bolumler`, `makale-duzenle`, `bolum-form`, `yeni`) — bunlar "liste" değil, form tasarımı ayrı bir iş. Revizyon/red notu uyarı kutuları (`bg-orange-50 border-orange-200`) bilerek dokunulmadı — kart/buton sistemi değil, semantik bir uyarı rengi. Doğrulama: `view:cache` hatasız, `npm run build` hatasız, **82/82 test yeşil**, curl ile oturum açıp her sayfa 200 döndüğü ve eski ham class'ların hiç kalmadığı teyit edildi (tarayıcı ekran görüntüsü alınamadı — bu ortamda Playwright/chromium-cli kurulu değil). |
| 7 | Abonelik sayfası | ⏳ Bekliyor | — |
| 8 | Anasayfa pilleri gerçek/tıklanabilir + `/kitaplar` katalog sayfası | ✅ Tamamlandı (2026-08-16) | Önceden "Yeni Çıkanlar" dışındaki pil'ler (Çok Satanlar/Editörün Seçkisi/Fırsatlar) sadece görsel/pasifti, anasayfa da her zaman en fazla 6 kitap gösteriyordu ("Kitaplar" kutusundaki sayı da bu 6'lık dilimin sayısını gösterip yanlış bir toplam veriyordu — 12 yayında kitap varken "6" yazıyordu). Şimdi: **`App\Enums\BookShelf`** enum'u pil mantığını tek yerden yönetiyor (`yeni`/`cok-satanlar`/`editorun-seckisi`/`firsatlar`, her biri kendi sorgusu+boş mesajıyla). `HomeController` artık `?raf=` query param'ına göre ilgili rafın ilk 6 kitabını + gerçek toplam yayında kitap sayısını gösteriyor; pil'ler artık gerçek `<a>` linki (Turbo ile SPA geçişi). "Yeni Çıkanlar" başlığının yanına **"Tümünü Gör →"** eklendi, yeni **`/kitaplar`** rotası + `BookCatalogController` + `books/index.blade.php` ile aynı 4 pil'in **tam listesi sayfalanmış** (18/sayfa) olarak görülebiliyor. "Kitaplar" stat kutusu artık `/kitaplar`'a giden bir link ve gerçek toplamı gösteriyor. **Altyapı (bilerek şimdiden kuruldu, gerçek veri gelene kadar demo ile dolduruldu):** `books` tablosuna `is_editors_pick` (boolean) ve `discount_price` (nullable decimal) sütunları eklendi (`Book` model: `scopeEditorsPick`, `scopeOnSale`, `scopeBestsellers` — bu sonuncusu **yeni sütun gerektirmedi**, var olan `purchases` ilişkisinden `withCount`+sıralama ile türetiliyor, sahte sayaç değil). `DemoContentSeeder`'a: 4 kitap `is_editors_pick=true`, 4 kitap `discount_price` dolu, ve "Çok Satanlar"ın boş görünmemesi için 6 adet demo okur hesabı (`demo.okur1..6@evrenkent.test`) + bu hesaplarla dağılımlı satın alma kayıtları eklendi — **bunlar canlıya taşınmadan önce silinmeli**, bkz. `DEPLOYMENT.md`. Fiyat gösterimi: `discount_price` doluysa kart üzerinde eski fiyat üstü çizili + indirimli fiyat gösteriliyor (hem anasayfa hem katalog kartlarında). Doğrulama: migration+seed sorunsuz, `view:cache`/`npm run build` hatasız, **82/82 test yeşil**, curl ile her `raf` değeri için doğru kart sayısı/boş durum teyit edildi. **Kapsam dışı:** Dergiler/Sözlükler türleri hâlâ "Yakında" (ayrı fazlar, bu işin konusu değildi). |

## Demo veri

- **2026-08-13:** `DemoContentSeeder`'a 13 yeni kitap eklendi (toplam 20 kitap,
  12'si "Yayında"), `php artisan db:seed --class=DemoContentSeeder` ile
  yerel DB'ye işlendi — anasayfa/kitaplığım gibi listeleme sayfaları artık
  daha gerçekçi görünüyor (2 kitap yerine 6+ kitap gridi).
  Seeder idempotent (`firstOrCreate` ile slug bazlı) — tekrar çalıştırmak
  güvenli, var olanları çoğaltmaz.
- **Not (bu fazın kapsamı dışı, ileride bakılabilir):** Temel `DatabaseSeeder`
  (proje kurulumundan beri var, benim eklemem değil) `author@evrenkent.test`
  hesabının adını literal olarak **"Yazar"** koymuş — bu yüzden bazı kitap
  kartlarında yazar adı olarak "YAZAR" görünüyor. Kozmetik bir seed-data
  detayı, gerçek bir isimle değiştirilmesi istenirse `DatabaseSeeder.php`'de.

## Değişen dosyalar (kümülatif)

- `tailwind.config.js` — `brand` renk skalası eklendi
- `resources/css/app.css` — bileşen sınıfları (`@layer components`) eklendi
- `resources/views/layouts/public.blade.php` — header restyle
- `resources/views/home.blade.php` — kategori sekmeleri, kart stilleri
- `app/Http/Controllers/BookController.php` — `categories` eager-load, `chapterCount`, `relatedBooks`
- `resources/views/books/show.blade.php` — sticky satın alma kartı, kategori etiketleri, öneri şeridi
- `resources/views/components/panel-nav.blade.php` — panel menüsü (yeni, `layouts/panel.blade.php`'den çıkarıldı)
- `resources/views/layouts/panel.blade.php` — sol menü artık `<x-panel-nav />` kullanıyor
- `resources/js/app.js` — Alpine.js zaten kuruluydu (Breeze ile geldi), panel menüsü için ek paket gerekmedi
- `database/seeders/DemoContentSeeder.php` — 13 yeni kitap örneği
- `resources/views/panel/kitapligim.blade.php` — ham Tailwind yerine `.card`/`.pill-tag`/`.btn-*` sınıflarına geçiş

## Her adımdan sonra yapılan doğrulama

1. `npm run build` — Vite derlemesi hatasız
2. `php artisan view:cache` — tüm Blade şablonları syntax hatasız derleniyor (sonra `view:clear` ile geri alınıyor, dev ortamı bozulmasın diye)
3. Gerçek sunucuda (`php artisan serve`) ekran görüntüsü alınıp mockup ile karşılaştırılıyor
