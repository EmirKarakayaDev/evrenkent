<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Evrenkent') }} — @yield('title', 'Panelim')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-paper text-slate-800">
        <nav class="bg-white border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-900 text-white">
                            <x-heroicon-o-book-open class="w-4 h-4" />
                        </span>
                        <span class="font-serif text-lg font-semibold tracking-tight text-slate-900">Evrenkent</span>
                    </a>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-slate-500">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">Çıkış Yap</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex gap-10">
            <aside class="w-60 shrink-0">
                <x-panel-nav />
            </aside>

            <main class="flex-1 min-w-0">
                @yield('content')
            </main>
        </div>
    </body>
</html>
