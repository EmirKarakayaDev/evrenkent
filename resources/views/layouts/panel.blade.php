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
    <body class="font-sans antialiased bg-orange-50 text-slate-800">
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
                @php
                    $navGroups = [
                        'Kişisel Kütüphanem' => [
                            'panel.index' => ['Kitaplığım', route('panel.index')],
                            'panel.favorilerim' => ['Favorilerim', route('panel.favorilerim')],
                            'panel.okuma-listem' => ['Okuma Listem', route('panel.okuma-listem')],
                            'panel.okuduklarim' => ['Okuduklarım', route('panel.okuduklarim')],
                        ],
                        'Çalışma Alanım' => [
                            'panel.defterim' => ['Defterim', route('panel.defterim')],
                            'panel.notlarim' => ['Notlarım', route('panel.notlarim')],
                            'panel.alintilarim' => ['Alıntılarım', route('panel.alintilarim')],
                        ],
                        'Alışveriş ve Abonelik' => [
                            'panel.satin-aldiklarim' => ['Satın Aldıklarım', route('panel.satin-aldiklarim')],
                            'panel.aboneligim' => ['Aboneliğim', route('panel.aboneligim')],
                        ],
                        'Hesap ve Destek' => [
                            'profile.edit' => ['Ayarlar', route('profile.edit')],
                            'panel.yardim' => ['Yardım Merkezi', route('panel.yardim')],
                            'panel.iletisim' => ['İletişim', route('panel.iletisim')],
                        ],
                    ];
                @endphp

                @if (auth()->user()->hasRole('yazar'))
                    <div class="mb-7">
                        <div class="text-xs font-semibold text-orange-700 uppercase tracking-wider mb-2.5">Yayın Yönetimi</div>
                        <div class="space-y-0.5">
                            @foreach ([
                                'panel.yayinlarim.index' => ['Yayınlarım', route('panel.yayinlarim.index')],
                                'panel.yayinlarim.taslaklarim' => ['Taslaklarım', route('panel.yayinlarim.taslaklarim')],
                                'panel.yayinlarim.gonderilenler' => ['Gönderilenler', route('panel.yayinlarim.gonderilenler')],
                                'panel.yayinlarim.geri-donenler' => ['Geri Dönenler', route('panel.yayinlarim.geri-donenler')],
                                'panel.yayinlarim.yayinlananlar' => ['Yayınlananlar', route('panel.yayinlarim.yayinlananlar')],
                                'panel.yayinlarim.istatistiklerim' => ['İstatistiklerim', route('panel.yayinlarim.istatistiklerim')],
                            ] as $routeName => [$label, $href])
                                <a href="{{ $href }}" class="block px-3 py-1.5 rounded-md text-sm {{ request()->routeIs($routeName) ? 'bg-orange-50 text-orange-800 font-medium ring-1 ring-inset ring-orange-200' : 'text-slate-600 hover:bg-slate-100' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @foreach ($navGroups as $group => $links)
                    <div class="mb-7">
                        <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2.5">{{ $group }}</div>
                        <div class="space-y-0.5">
                            @foreach ($links as $routeName => [$label, $href])
                                <a href="{{ $href }}" class="block px-3 py-1.5 rounded-md text-sm {{ request()->routeIs($routeName) ? 'bg-slate-900 text-white font-medium' : 'text-slate-600 hover:bg-slate-100' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </aside>

            <main class="flex-1 min-w-0">
                @yield('content')
            </main>
        </div>
    </body>
</html>
