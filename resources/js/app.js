import '@hotwired/turbo';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Turbo yalnızca <body>'yi değiştirir, JS ortamı sayfalar arası canlı kalır —
// bu yüzden panel sidebar'ının açık/kapalı durumu Alpine.store'da tutulur.
// Böylece "Kitaplığım -> Favorilerim" gibi bir Turbo geçişinde sidebar
// pozisyonunu kaybetmez (aksi halde her body swap'inde x-data sıfırlanırdı).
Alpine.store('ui', {
    sidebarOpen: true,
});

Alpine.start();
