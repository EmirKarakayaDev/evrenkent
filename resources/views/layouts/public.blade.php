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
            <header class="sticky top-0 z-20 bg-paper/90 backdrop-blur border-b border-slate-200">
                <div class="max-w-6xl mx-auto px-6">
                    <div class="flex justify-between h-16 items-center gap-6">
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

                        <div class="flex items-center gap-5 shrink-0 ms-auto">
                            {{-- Arama ve sepet: görsel iskelet — henüz işlevsel değil, "Yakında" ipucu bilinçli. --}}
                            <button type="button" title="Yakında" class="text-slate-400 cursor-not-allowed">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                            </button>

                            <button type="button" title="Yakında" class="flex items-center gap-1.5 text-sm text-slate-400 cursor-not-allowed">
                                <x-heroicon-o-shopping-bag class="w-5 h-5" />
                                Sepetim
                            </button>

                            @auth
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

            <div class="flex-1 flex items-start">
                @auth
                    {{-- Panel menüsü: overlay değil, normal akışta — açılınca içerik alanı daralır (YouTube'daki gibi).
                         İçteki w-72'lik sabit genişlik, dıştaki genişlik animasyonu sırasında metnin kırılmasını önler. --}}
                    <aside
                        :class="$store.ui.sidebarOpen ? 'w-72 border-r border-slate-200' : 'w-0 border-r-0'"
                        class="shrink-0 bg-white transition-all duration-200 sticky top-16 self-start h-[calc(100vh-4rem)] overflow-x-hidden overflow-y-auto"
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
    </body>
</html>
