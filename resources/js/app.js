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

Alpine.start();
