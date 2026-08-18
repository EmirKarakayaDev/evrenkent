<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Evrenkent') }} — Süper Admin Paneli — @yield('title', 'Ana Sayfa')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600,600i|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{--
        Süper Admin paneli bilerek genel public kabuğu (layouts/public.blade.php) kullanmıyor —
        kullanıcı mockup'a (dosyalar/2.4-)...png) birebir yakın, koyu lacivert bir tasarım
        istedi. Mobil pattern (fixed+translate-x overlay drawer, Alpine.store('ui').sidebarOpen,
        backdrop, .sidebar-scroll) yine de public kabuktakiyle bire bir aynı — yeni bir mobil
        davranış icat edilmiyor, sadece renk paleti değişiyor.
    --}}
    <body class="font-sans antialiased bg-slate-100 text-slate-800" x-data @keydown.window.ctrl.k.prevent="$refs.adminSearch?.focus()">
        <div class="min-h-screen flex flex-col">
            <header class="sticky top-0 z-20 bg-slate-950 border-b border-slate-800">
                <div class="px-6">
                    <div class="grid grid-cols-[auto_1fr_auto] h-16 items-center gap-6">
                        <div class="flex items-center gap-4 shrink-0">
                            <button type="button" title="Menü" @click="$store.ui.sidebarOpen = !$store.ui.sidebarOpen" class="text-slate-300 hover:text-white transition-colors">
                                <x-heroicon-o-bars-3 class="w-6 h-6" />
                            </button>

                            <a href="{{ route('panel.adminpanel.index') }}" class="flex items-center gap-2.5 group">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-slate-900">
                                    <x-heroicon-o-book-open class="w-4 h-4" />
                                </span>
                                <span class="font-serif text-lg font-semibold tracking-tight text-white leading-tight">
                                    Evrenkent
                                    <span class="block text-[10px] font-sans font-medium tracking-widest text-slate-400 uppercase leading-none">Süper Admin Paneli</span>
                                </span>
                            </a>
                        </div>

                        <div class="hidden sm:block w-full max-w-md mx-auto">
                            <form method="GET" action="{{ route('arama') }}" class="relative">
                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2" />
                                <input x-ref="adminSearch" type="text" name="q" value="{{ request('q') }}" placeholder="Ara…" class="w-full rounded-lg border border-slate-700 bg-slate-900 pl-9 pr-14 py-2 text-sm text-slate-100 placeholder:text-slate-500 focus:outline-none focus:border-slate-500">
                                <span class="hidden md:inline absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 border border-slate-700 rounded px-1.5 py-0.5">Ctrl+K</span>
                            </form>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-5 shrink-0 justify-self-end">
                            <a href="{{ route('arama') }}" title="Ara" class="sm:hidden inline-flex items-center text-slate-300 hover:text-white transition-colors">
                                <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                            </a>

                            {{-- x-notifications-bell kendi renklerini (slate-500/900) sabit taşıyor;
                                 koyu üst barda kontrast düşük kalmasın diye beyaz bir daire içine alıyoruz. --}}
                            <div class="bg-white rounded-full p-1.5 shadow-sm">
                                <x-notifications-bell />
                            </div>

                            <div class="relative inline-flex items-center" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" @click="open = !open" class="flex items-center gap-2.5 text-left">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-700 text-white text-sm font-medium">
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    </span>
                                    <span class="hidden md:block leading-tight">
                                        <span class="block text-sm font-medium text-white">{{ auth()->user()->name }}</span>
                                        <span class="block text-xs text-slate-400">Evrenkent Platformu</span>
                                    </span>
                                </button>

                                <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 top-full mt-3 w-48 card py-1 z-30">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Ayarlar</a>
                                    <a href="{{ url('/admin') }}" data-turbo="false" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Filament Yönetim Paneli</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Çıkış Yap</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 flex">
                <div
                    x-show="$store.ui.sidebarOpen"
                    x-cloak
                    x-transition.opacity
                    @click="$store.ui.sidebarOpen = false"
                    class="fixed inset-0 top-16 z-30 bg-slate-900/60 lg:hidden"
                ></div>

                <aside
                    @click="if (window.innerWidth < 1024) $store.ui.sidebarOpen = false"
                    :class="$store.ui.sidebarOpen
                        ? 'translate-x-0 lg:w-72 lg:border-r lg:border-slate-800'
                        : '-translate-x-full lg:translate-x-0 lg:w-0 lg:border-r-0'"
                    class="sidebar-scroll fixed top-16 bottom-0 left-0 z-40 w-72 shadow-xl bg-slate-950 transition-transform duration-200 overflow-x-hidden overflow-y-auto lg:shadow-none lg:z-auto lg:static lg:sticky lg:bottom-auto lg:h-[calc(100vh-4rem)] lg:self-start lg:shrink-0 lg:transition-[width]"
                >
                    <div class="w-72 p-5">
                        <x-admin-nav />
                    </div>
                </aside>

                <div class="flex-1 min-w-0 flex flex-col">
                    <main class="flex-1 max-w-7xl mx-auto px-6 py-8 w-full">
                        @if (session('status'))
                            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-md px-4 py-2.5">
                                {{ session('status') }}
                            </div>
                        @endif

                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
