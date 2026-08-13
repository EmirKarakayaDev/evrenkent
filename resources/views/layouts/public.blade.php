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
        <div class="min-h-screen flex flex-col">
            <header class="sticky top-0 z-20 bg-paper/90 backdrop-blur border-b border-slate-200">
                <div class="max-w-6xl mx-auto px-6">
                    <div class="flex justify-between h-16 items-center">
                        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-900 text-white">
                                <x-heroicon-o-book-open class="w-4 h-4" />
                            </span>
                            <span class="font-serif text-lg font-semibold tracking-tight text-slate-900">Evrenkent</span>
                        </a>

                        <div class="flex items-center gap-6">
                            @auth
                                <a href="{{ auth()->user()->redirectPath() }}" class="text-sm text-slate-600 hover:text-slate-900 transition-colors">
                                    Panelim
                                </a>
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
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                                    Kayıt Ol
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

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
    </body>
</html>
