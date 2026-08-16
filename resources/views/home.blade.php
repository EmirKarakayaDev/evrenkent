@extends('layouts.public')

@section('title', 'Ana Sayfa')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
        <a href="{{ route('kitaplar.index') }}" class="flex items-center gap-4 rounded-lg border border-brand-300 bg-white p-4 hover:border-brand-400 transition-colors">
            <x-heroicon-o-book-open class="w-7 h-7 text-brand-500 shrink-0" />
            <div>
                <div class="font-medium text-slate-900">Kitaplar</div>
                <div class="text-sm text-slate-500">{{ $totalBooks }} eser listeleniyor</div>
            </div>
        </a>
        <a href="{{ route('dergiler.index') }}" class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 hover:border-slate-300 transition-colors">
            <x-heroicon-o-newspaper class="w-7 h-7 text-slate-400 shrink-0" />
            <div>
                <div class="font-medium text-slate-900">Dergiler</div>
                <div class="text-sm text-slate-500">{{ $totalIssues }} sayı listeleniyor</div>
            </div>
        </a>
        <div title="Yakında" class="flex items-center gap-4 rounded-lg border border-slate-200 bg-white p-4 text-slate-400 cursor-not-allowed">
            <x-heroicon-o-language class="w-7 h-7 shrink-0" />
            <div>
                <div class="font-medium">Sözlükler</div>
                <div class="text-sm">Yakında</div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap gap-2.5 mb-10">
        @foreach (\App\Enums\BookShelf::cases() as $tab)
            <a href="{{ route('home', ['raf' => $tab->value]) }}" class="{{ $shelf === $tab ? 'pill-active' : 'pill-idle' }}">
                <x-dynamic-component :component="match ($tab) {
                    \App\Enums\BookShelf::YeniCikanlar => $shelf === $tab ? 'heroicon-s-star' : 'heroicon-o-star',
                    \App\Enums\BookShelf::CokSatanlar => 'heroicon-o-fire',
                    \App\Enums\BookShelf::EditorunSeckisi => 'heroicon-o-sparkles',
                    \App\Enums\BookShelf::Firsatlar => 'heroicon-o-tag',
                }" class="w-4 h-4" />
                {{ $tab->label() }}
            </a>
        @endforeach
    </div>

    <div class="flex items-baseline justify-between mb-5">
        <h2 class="font-serif text-xl font-semibold text-slate-900">{{ $shelf->label() }}</h2>
        <a href="{{ route('kitaplar.index', ['raf' => $shelf->value]) }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
            Tümünü Gör →
        </a>
    </div>

    @if ($books->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            {{ $shelf->emptyMessage() }}
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach ($books as $book)
                <a href="{{ route('kitaplar.show', $book) }}" class="group block card-hover overflow-hidden">
                    <x-book-cover :book="$book" class="aspect-[3/4]" />
                    <div class="p-3">
                        <div class="text-xs text-brand-600 font-medium uppercase tracking-wide">{{ $book->author->name }}</div>
                        <div class="font-medium text-slate-900 text-sm truncate mt-0.5">{{ $book->title }}</div>
                        <div class="text-sm mt-1">
                            @if ($book->discount_price !== null)
                                <span class="text-slate-400 line-through mr-1.5">{{ number_format($book->price, 2, ',', '.') }} TL</span>
                                <span class="text-brand-700 font-medium">{{ number_format($book->discount_price, 2, ',', '.') }} TL</span>
                            @else
                                <span class="text-slate-500">{{ number_format($book->price, 2, ',', '.') }} TL</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
