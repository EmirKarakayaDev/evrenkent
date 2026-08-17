{{--
    Panel menüsü — hem panel sayfalarının sol sütununda (layouts/panel.blade.php)
    hem de public sayfalardaki hamburger drawer'ında (layouts/public.blade.php)
    kullanılır. Tek yerden değişir, ikisi de aynı kalır.
--}}
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
            'panel.sepetim' => ['Sepetim', route('panel.sepetim')],
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
        <div class="text-xs font-semibold text-brand-700 uppercase tracking-wider mb-2.5">Yayın Yönetimi</div>
        <div class="space-y-0.5">
            @foreach ([
                'panel.yayinlarim.index' => ['Yayınlarım', route('panel.yayinlarim.index')],
                'panel.yayinlarim.taslaklarim' => ['Taslaklarım', route('panel.yayinlarim.taslaklarim')],
                'panel.yayinlarim.gonderilenler' => ['Gönderilenler', route('panel.yayinlarim.gonderilenler')],
                'panel.yayinlarim.geri-donenler' => ['Geri Dönenler', route('panel.yayinlarim.geri-donenler')],
                'panel.yayinlarim.yayinlananlar' => ['Yayınlananlar', route('panel.yayinlarim.yayinlananlar')],
                'panel.yayinlarim.istatistiklerim' => ['İstatistiklerim', route('panel.yayinlarim.istatistiklerim')],
            ] as $routeName => [$label, $href])
                <a href="{{ $href }}" class="block px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs($routeName) ? 'bg-brand-50 text-brand-800 font-medium ring-1 ring-inset ring-brand-200' : 'text-slate-600 hover:bg-slate-100' }}">
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
                <a href="{{ $href }}" class="block px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs($routeName) ? 'bg-slate-900 text-white font-medium' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
@endforeach
