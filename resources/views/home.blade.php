@extends('layouts.public')

@section('title', 'Ana Sayfa')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
        <div class="flex items-center gap-4 rounded-lg border border-brand-300 bg-white p-4">
            <x-heroicon-o-book-open class="w-7 h-7 text-brand-500 shrink-0" />
            <div>
                <div class="font-medium text-slate-900">Kitaplar</div>
                <div class="text-sm text-slate-500">{{ $books->count() }} eser listeleniyor</div>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 text-slate-400">
            <x-heroicon-o-newspaper class="w-7 h-7 shrink-0" />
            <div>
                <div class="font-medium">Dergiler</div>
                <div class="text-sm">Yakında</div>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 text-slate-400">
            <x-heroicon-o-language class="w-7 h-7 shrink-0" />
            <div>
                <div class="font-medium">Sözlükler</div>
                <div class="text-sm">Yakında</div>
            </div>
        </div>
    </div>

    {{-- Kategori sekmeleri: görsel iskelet — "Yeni Çıkanlar" dışındakiler henüz veriyle bağlı değil. --}}
    <div class="flex flex-wrap gap-2.5 mb-10">
        <span class="pill-active">
            <x-heroicon-s-star class="w-4 h-4" />
            Yeni Çıkanlar
        </span>
        <span title="Yakında" class="pill-idle cursor-not-allowed">
            <x-heroicon-o-fire class="w-4 h-4" />
            Çok Satanlar
        </span>
        <span title="Yakında" class="pill-idle cursor-not-allowed">
            <x-heroicon-o-sparkles class="w-4 h-4" />
            Editörün Seçkisi
        </span>
        <span title="Yakında" class="pill-idle cursor-not-allowed">
            <x-heroicon-o-tag class="w-4 h-4" />
            Fırsatlar
        </span>
    </div>

    <div class="flex items-baseline justify-between mb-5">
        <h2 class="font-serif text-xl font-semibold text-slate-900">Yeni Çıkanlar</h2>
    </div>

    @if ($books->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Henüz yayınlanmış bir kitap yok.
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach ($books as $book)
                <a href="{{ route('kitaplar.show', $book) }}" class="group block card-hover overflow-hidden">
                    <x-book-cover :book="$book" class="aspect-[3/4]" />
                    <div class="p-3">
                        <div class="text-xs text-brand-600 font-medium uppercase tracking-wide">{{ $book->author->name }}</div>
                        <div class="font-medium text-slate-900 text-sm truncate mt-0.5">{{ $book->title }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ number_format($book->price, 2, ',', '.') }} TL</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
