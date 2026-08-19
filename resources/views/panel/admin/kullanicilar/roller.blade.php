@extends('layouts.admin-panel')

@section('title', 'Roller ve Yetkiler')

@section('content')
    <div class="mb-6">
        <h1 class="font-serif text-xl font-semibold text-slate-900">Roller ve Yetkiler</h1>
        <p class="text-sm text-slate-500 mt-1">
            Evrenkent şu an 4 sabit rol üzerinden çalışıyor — dinamik izin/rol oluşturma
            altyapısı yok, bu yüzden burada uydurma bir "izin matrisi" göstermek yerine
            gerçek kullanıcı sayılarıyla birlikte her rolün gerçekten neye yetkisi olduğu
            özetleniyor. Bir kullanıcının rolünü değiştirmek için
            <a href="{{ route('panel.adminpanel.kullanicilar.index') }}" class="underline">Kullanıcılar</a>
            listesinden düzenleyebilirsiniz.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($roleSummaries as $name => $summary)
            <div class="card p-5">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <h2 class="font-medium text-slate-900">{{ $summary['label'] }}</h2>
                    <span class="pill-tag text-xs px-2 py-0.5">{{ $summary['count'] }} kullanıcı</span>
                </div>
                <p class="text-sm text-slate-500">{{ $summary['description'] }}</p>
                <a href="{{ route('panel.adminpanel.kullanicilar.index', ['rol' => $name]) }}" class="inline-flex items-center gap-1 text-sm text-brand-700 hover:text-brand-800 mt-3 transition-colors">
                    Bu roldeki kullanıcıları gör <x-heroicon-o-arrow-right class="w-3.5 h-3.5" />
                </a>
            </div>
        @endforeach
    </div>
@endsection
