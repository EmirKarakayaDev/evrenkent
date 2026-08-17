@extends('layouts.panel')

@section('title', 'Sayılarım')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
        <h1 class="font-serif text-xl font-semibold text-slate-900">Sayılarım</h1>
        <a href="{{ route('panel.dergi.sayilarim.yeni') }}" class="btn-dark btn-sm">
            <x-heroicon-o-plus class="w-4 h-4" /> Yeni Sayı Oluştur
        </a>
    </div>

    <div class="flex flex-wrap gap-2.5 mb-8">
        <a href="{{ route('panel.dergi.sayilarim') }}" class="{{ ! $status ? 'pill-active' : 'pill-idle' }}">Tümü</a>
        @foreach (\App\Enums\ContentStatus::cases() as $case)
            <a href="{{ route('panel.dergi.sayilarim', ['durum' => $case->value]) }}" class="{{ $status === $case ? 'pill-active' : 'pill-idle' }}">
                {{ $case->label() }}
            </a>
        @endforeach
    </div>

    @if ($issues->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-newspaper class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Bu durumda bir sayınız yok.
        </div>
    @else
        <div class="card divide-y divide-slate-100">
            @foreach ($issues as $issue)
                <div class="flex items-center justify-between gap-3 px-5 py-4 flex-wrap sm:flex-nowrap">
                    <div class="flex items-start gap-3 min-w-0">
                        <x-heroicon-o-newspaper class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                        <div class="min-w-0">
                            <div class="font-medium text-slate-900 truncate">{{ $issue->title }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">Sayı {{ $issue->issue_number }}</div>
                            <x-status-badge :status="$issue->status" class="mt-1.5" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        <a href="{{ route('dergiler.show', $issue) }}" class="btn-outline btn-sm">Görüntüle</a>
                        {{-- Onaya gönderildikten sonra (Süper Admin onaylayana/reddedene kadar)
                             düzenlenemiyor — buton o durumda hiç gösterilmiyor, yazarın kendi
                             yayın listesindeki (_liste.blade.php) @can deseniyle aynı. --}}
                        @can('update', $issue)
                            <a href="{{ route('panel.dergi.sayilarim.duzenle', $issue) }}" class="btn-dark btn-sm">Düzenle</a>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
