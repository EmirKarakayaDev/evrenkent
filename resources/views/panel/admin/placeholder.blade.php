@extends('layouts.admin-panel')

@section('title', $title)

@section('content')
    <div class="max-w-lg mx-auto text-center py-20">
        <div class="mx-auto w-14 h-14 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center mb-5">
            <x-heroicon-o-wrench-screwdriver class="w-6 h-6" />
        </div>
        <h1 class="font-serif text-xl font-semibold text-slate-900 mb-2">{{ $title }}</h1>
        <p class="text-sm text-slate-500">
            Bu bölüm için altyapı henüz hazır değil, bu yüzden burada sahte veri göstermek yerine
            dürüstçe "yakında" diyoruz. Gerçek veri bağlanınca bu sayfa da diğerleri gibi canlı
            olacak.
        </p>
        <a href="{{ route('panel.adminpanel.index') }}" class="btn-outline btn-sm mt-6 inline-flex">
            Ana Sayfaya Dön
        </a>
    </div>
@endsection
