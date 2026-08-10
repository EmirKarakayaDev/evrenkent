@extends('layouts.public')

@section('title', 'Ana Sayfa')

@section('content')
    <div class="mb-10">
        <p class="font-serif italic text-slate-500 text-lg">Okumanın yeni bir evreni</p>
        <h1 class="font-serif text-3xl font-semibold text-slate-900 mt-1">Kitaplar, dergiler ve sözlükler — tek yerde</h1>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
        <div class="flex items-center gap-4 rounded-lg border border-slate-900 bg-white p-4">
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-900 text-white shrink-0">
                <x-heroicon-o-book-open class="w-5 h-5" />
            </span>
            <div>
                <div class="font-medium text-slate-900">Kitaplar</div>
                <div class="text-sm text-slate-500">{{ $books->count() }} eser listeleniyor</div>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 text-slate-400">
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 shrink-0">
                <x-heroicon-o-newspaper class="w-5 h-5" />
            </span>
            <div>
                <div class="font-medium">Dergiler</div>
                <div class="text-sm">Yakında</div>
            </div>
        </div>
        <div class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 text-slate-400">
            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 shrink-0">
                <x-heroicon-o-language class="w-5 h-5" />
            </span>
            <div>
                <div class="font-medium">Sözlükler</div>
                <div class="text-sm">Yakında</div>
            </div>
        </div>
    </div>

    <div class="flex items-baseline justify-between mb-5">
        <h2 class="font-serif text-xl font-semibold text-slate-900">Yeni Çıkanlar</h2>
    </div>

    @if ($books->isEmpty())
        <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
            <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Henüz yayınlanmış bir kitap yok.
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach ($books as $book)
                <div class="group">
                    <div class="aspect-[3/4] bg-slate-100 border border-slate-200 rounded-md flex items-center justify-center text-slate-300 group-hover:border-slate-400 transition-colors">
                        <x-heroicon-o-book-open class="w-7 h-7" />
                    </div>
                    <div class="pt-3">
                        <div class="text-xs text-orange-700 font-medium uppercase tracking-wide">{{ $book->author->name }}</div>
                        <div class="font-medium text-slate-900 text-sm truncate mt-0.5">{{ $book->title }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ number_format($book->price, 2, ',', '.') }} TL</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
