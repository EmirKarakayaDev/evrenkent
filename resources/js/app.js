import '@hotwired/turbo';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Turbo yalnızca <body>'yi değiştirir, JS ortamı sayfalar arası canlı kalır —
// bu yüzden panel sidebar'ının açık/kapalı durumu Alpine.store'da tutulur.
// Böylece "Kitaplığım -> Favorilerim" gibi bir Turbo geçişinde sidebar
// pozisyonunu kaybetmez (aksi halde her body swap'inde x-data sıfırlanırdı).
// Başlangıç değeri ekran genişliğine göre: masaüstünde (lg ve üstü) açık,
// mobilde kapalı — mobilde sidebar artık push değil overlay (bkz.
// layouts/public.blade.php), açık gelmesi ekranın çoğunu kaplardı.
Alpine.store('ui', {
    sidebarOpen: window.matchMedia('(min-width: 1024px)').matches,
});

// Sepet sayacı (header rozeti) ve "sepete eklendi" toast'ı — sayfa yenilenmeden
// (fetch ile) güncellenebilsin diye Alpine store'da tutuluyor. Her Turbo geçişinde
// header'daki x-init sunucudan gelen gerçek sayıyla senkronlar (bkz. layouts/public.blade.php).
Alpine.store('cart', {
    count: 0,
    toast: { visible: false, title: '', url: '' },
    toastTimeout: null,
    showToast(title, url) {
        this.toast.title = title;
        this.toast.url = url;
        this.toast.visible = true;
        clearTimeout(this.toastTimeout);
        this.toastTimeout = setTimeout(() => {
            this.toast.visible = false;
        }, 4000);
    },
});

// Sidebar scroll pozisyonu — Turbo her geçişte <body>'yi (dolayısıyla <aside>'ı)
// baştan render ediyor, bu yüzden aşağı kaydırıp bir linke tıklayınca sidebar
// görsel olarak "sıfırlanıp" en başa dönüyordu. scroll event'i bubble etmediği
// için document üzerinde capture:true ile dinleniyor; pozisyon düz bir JS
// değişkeninde tutuluyor (bu da Turbo geçişleri arasında canlı kalıyor, aynı
// yukarıdaki store'lar gibi) ve her yeni sayfa render olduğunda geri uygulanıyor.
// Sidebar içeriği (aktif link vurgusu dahil) yine sunucudan taze geliyor —
// sadece scrollTop taşınıyor, data-turbo-permanent gibi tüm elementi
// "donduran" bir yöntem kullanılmadı çünkü o zaman aktif link vurgusu bir
// önceki sayfadan kalma, bayat kalırdı.
let sidebarScrollTop = 0;
document.addEventListener('scroll', (event) => {
    if (event.target?.classList?.contains('sidebar-scroll')) {
        sidebarScrollTop = event.target.scrollTop;
    }
}, true);
// turbo:load değil turbo:render dinleniyor: Turbo yeni <body>'yi turbo:render'da
// takıyor, turbo:load ise ondan sonra (script'ler vs. bittiğinde) ayrı bir
// "tick"te geliyor — arada tarayıcı bir kare boyama fırsatı buluyor ve
// scrollTop=0 olan taze sidebar'ı bir anlığına gösterip sonra bizim düzeltmemizle
// "zıplıyordu" (kullanıcının bildirdiği "saniyelik en üste gelip düzelme").
// turbo:render'da düzeltmek aynı task içinde kalıp o ara kareyi ortadan kaldırıyor.
document.addEventListener('turbo:render', () => {
    const sidebar = document.querySelector('.sidebar-scroll');
    if (sidebar) {
        sidebar.scrollTop = sidebarScrollTop;
    }
});

Alpine.start();
