<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Evrenkent') }} — @yield('title', 'Ana Sayfa')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600,600i|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-paper text-slate-800">
        <div class="min-h-screen flex flex-col" x-data>
            {{-- Header artık içerikle aynı max-w-6xl kutusuna değil, tam genişliğe (edge-to-edge)
                 yayılıyor — YouTube tarzı: hamburger/logo gerçek sol kenara, sağdaki ikonlar gerçek
                 sağ kenara yakın duruyor, altındaki sidebar'ın sol kenarıyla dikey hizalı kalıyor.
                 Ana içerik (@yield('content')) hâlâ max-w-6xl ile ortalı, oranı değişmedi. --}}
            <header class="sticky top-0 z-20 bg-paper/90 backdrop-blur border-b border-slate-200">
                <div class="px-6">
                    <div class="grid grid-cols-[auto_1fr_auto] h-16 items-center gap-6">
                        <div class="flex items-center gap-4 shrink-0">
                            {{-- Hamburger: YouTube tarzı — sayfayı örtmez, içerik alanını daraltarak yandan panel
                                 menüsünü açar/kapatır. Ziyaretçide gösterilecek bir menü içeriği (mega-menü) henüz
                                 yok, o yüzden ziyaretçide hiç render edilmiyor. --}}
                            @auth
                                <button type="button" title="Menü" @click="$store.ui.sidebarOpen = !$store.ui.sidebarOpen" class="text-slate-700 hover:text-slate-900 transition-colors">
                                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                                </button>
                            @endauth

                            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-900 text-white">
                                    <x-heroicon-o-book-open class="w-4 h-4" />
                                </span>
                                <span class="font-serif text-lg font-semibold tracking-tight text-slate-900">Evrenkent</span>
                            </a>
                        </div>

                        {{-- Arama: kitap/dergi/makale üzerinde gerçek (LIKE tabanlı) arama yapan
                             /arama sayfasına GET form ile gönderiyor. --}}
                        <div class="hidden sm:block w-full max-w-md mx-auto">
                            <form method="GET" action="{{ route('arama') }}" class="relative">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kitap, yazar veya konu ara…" class="w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-brand-300">
                            </form>
                        </div>

                        <div class="flex items-center gap-5 shrink-0">
                            <a href="{{ route('arama') }}" title="Ara" class="sm:hidden text-slate-500 hover:text-slate-900 transition-colors">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                            </a>

                            @auth
                                {{-- Sayaç Alpine store'da (Alpine.store('cart').count) tutuluyor ki "Sepete Ekle"
                                     fetch ile tıklanınca sayfa yenilenmeden güncellenebilsin (bkz.
                                     x-add-to-cart-button). x-init her yüklemede/Turbo geçişinde sunucudan
                                     gelen gerçek sayıyla senkronluyor. --}}
                                <a href="{{ route('panel.sepetim') }}" title="Sepetim" x-data x-init="$store.cart.count = {{ auth()->user()->cartItems()->count() }}" class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-900 transition-colors">
                                    {{-- Rozet ikonun sağ-üst köşesine kendi boyutunun yarısı kadar
                                         translate edilerek sabitleniyor (badge'in kendi genişliğinden
                                         bağımsız, standart "corner badge" tekniği) — bu sayede "Sepetim"
                                         yazısına taşmıyor, kaç haneli sayı olursa olsun doğru köşede kalır. --}}
                                    <span class="relative inline-flex shrink-0">
                                        <x-heroicon-o-shopping-bag class="w-5 h-5" />
                                        <span x-show="$store.cart.count > 0" x-cloak x-text="$store.cart.count" class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-brand-500 text-white text-[10px] leading-[1.1rem] text-center"></span>
                                    </span>
                                    Sepetim
                                </a>
                            @else
                                <a href="{{ route('login') }}" title="Sepetim" class="flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 transition-colors">
                                    <x-heroicon-o-shopping-bag class="w-5 h-5" />
                                    Sepetim
                                </a>
                            @endauth

                            @auth
                                <x-notifications-bell />

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">
                                        Çıkış Yap
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">
                                    Giriş Yap
                                </a>
                                <a href="{{ route('register') }}" class="btn-dark btn-sm">
                                    Kayıt Ol
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 flex">
                @auth
                    {{-- Panel menüsü: overlay değil, normal akışta — açılınca içerik alanı daralır (YouTube'daki gibi).
                         İçteki w-72'lik sabit genişlik, dıştaki genişlik animasyonu sırasında metnin kırılmasını önler. --}}
                    <aside
                        :class="$store.ui.sidebarOpen ? 'w-72 border-r border-slate-200' : 'w-0 border-r-0'"
                        class="shrink-0 bg-paper transition-all duration-200 sticky top-16 self-start h-[calc(100vh-4rem)] overflow-x-hidden overflow-y-auto"
                    >
                        <div class="w-72 p-5">
                            <x-panel-nav />
                        </div>
                    </aside>
                @endauth

                <div class="flex-1 min-w-0 flex flex-col">
                    <main class="flex-1 max-w-6xl mx-auto px-6 py-10 w-full">
                        @if (session('status'))
                            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-md px-4 py-2.5">
                                {{ session('status') }}
                            </div>
                        @endif

                        @yield('content')
                    </main>

                    <footer class="border-t border-slate-200 py-8">
                        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-2 text-sm text-slate-400">
                            <span>&copy; {{ now()->year }} Evrenkent</span>
                            <span class="font-serif italic">Okumanın yeni bir evreni</span>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        @auth
            <x-cart-toast />
        @endauth
    </body>
</html>
