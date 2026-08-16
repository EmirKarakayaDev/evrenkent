@extends('layouts.public')

@section('title', $category ? $category->name : 'Kitaplar')

@section('content')
    @if ($category)
        <div class="flex items-baseline gap-3 mb-5">
            <h1 class="font-serif text-xl font-semibold text-slate-900">{{ $category->name }}</h1>
            <a href="{{ route('kitaplar.index') }}" class="text-sm text-slate-400 hover:text-slate-600 transition-colors">Tüm kitaplar →</a>
        </div>
    @else
        <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Kitaplar</h1>

        <div class="flex flex-wrap gap-2.5 mb-8">
            @foreach (\App\Enums\BookShelf::cases() as $tab)
                <a href="{{ route('kitaplar.index', ['raf' => $tab->value]) }}" class="{{ $shelf === $tab ? 'pill-active' : 'pill-idle' }}">
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
    @endif

    @if ($books->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            {{ $category ? 'Bu kategoride yayınlanmış bir kitap yok.' : $shelf->emptyMessage() }}
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

        <div class="mt-8">
            {{ $books->links() }}
        </div>
    @endif
@endsection
