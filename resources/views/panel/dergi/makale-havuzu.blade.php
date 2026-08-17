@extends('layouts.panel')

@section('title', 'Makale Havuzu')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Makale Havuzu</h1>

    <div class="flex flex-wrap gap-2.5 mb-8">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route('panel.dergi.makale-havuzu', ['durum' => $key]) }}" class="{{ $activeTab === $key ? 'pill-active' : 'pill-idle' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    @if ($articles->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-document-text class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Bu sekmede bir makale yok.
        </div>
    @else
        <x-article-pool-table :articles="$articles" />

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
