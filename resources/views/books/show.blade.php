@extends('layouts.public')

@section('title', $book->title)

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-[220px_1fr] gap-8">
        <div class="aspect-[3/4] bg-slate-100 border border-slate-200 rounded-md flex items-center justify-center text-slate-300 overflow-hidden">
            @if ($book->cover_image)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
            @else
                <x-heroicon-o-book-open class="w-10 h-10" />
            @endif
        </div>

        <div>
            <div class="text-xs text-orange-700 font-medium uppercase tracking-wide">{{ $book->author->name }}</div>
            <h1 class="font-serif text-2xl font-semibold text-slate-900 mt-1">{{ $book->title }}</h1>
            <x-status-badge :status="$book->status" class="mt-2" />

            @if ($book->description)
                <p class="text-slate-600 mt-4 whitespace-pre-line">{{ $book->description }}</p>
            @endif

            <div class="text-xl font-medium text-slate-900 mt-5">{{ number_format($book->price, 2, ',', '.') }} TL</div>

            @auth
                <div class="flex flex-wrap items-center gap-3 mt-5">
                    <form method="POST" action="{{ route('panel.favoriler.kitap.toggle', $book) }}">
                        @csrf
                        <button type="submit" class="text-sm px-4 py-2 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5">
                            <x-heroicon-o-heart class="w-4 h-4 {{ $hasFavorited ? 'text-orange-600' : '' }}" />
                            {{ $hasFavorited ? 'Favorilerde' : 'Favorile' }}
                        </button>
                    </form>

                    @if ($readingListItem && $readingListItem->status === \App\Enums\ReadingStatus::Tamamlandi)
                        <span class="text-sm px-4 py-2 rounded-md bg-slate-100 text-slate-500 inline-flex items-center gap-1.5">
                            <x-heroicon-o-check class="w-4 h-4" /> Okundu
                        </span>
                    @elseif ($readingListItem)
                        <span class="text-sm px-4 py-2 rounded-md bg-slate-100 text-slate-500 inline-flex items-center gap-1.5">
                            <x-heroicon-o-bookmark class="w-4 h-4" /> Okuma Listesinde
                        </span>
                    @else
                        <form method="POST" action="{{ route('panel.okuma-listesi.kitap.ekle', $book) }}">
                            @csrf
                            <button type="submit" class="text-sm px-4 py-2 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50 transition-colors inline-flex items-center gap-1.5">
                                <x-heroicon-o-bookmark class="w-4 h-4" /> Okuma Listesine Ekle
                            </button>
                        </form>
                    @endif

                    @if ($hasPurchased)
                        <span class="text-sm px-4 py-2 rounded-md bg-slate-900 text-white inline-flex items-center gap-1.5">
                            <x-heroicon-o-check-circle class="w-4 h-4" /> Satın Alındı
                        </span>
                    @elseif ($book->status === \App\Enums\ContentStatus::Yayinda)
                        <form method="POST" action="{{ route('panel.satin-al', $book) }}">
                            @csrf
                            <button type="submit" class="text-sm px-4 py-2 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-colors">
                                Satın Al
                            </button>
                        </form>
                    @endif
                </div>

                @if ($hasPurchased || $book->status === \App\Enums\ContentStatus::Yayinda)
                    <p class="text-sm text-slate-500 mt-4">
                        Bu kitaba not veya alıntı eklemek için
                        <a href="{{ route('panel.notlarim') }}" class="text-slate-900 underline">Notlarım</a> ya da
                        <a href="{{ route('panel.alintilarim') }}" class="text-slate-900 underline">Alıntılarım</a>
                        sayfasını kullanabilirsiniz (Kitap ID: {{ $book->id }}).
                    </p>
                @endif
            @else
                <p class="text-sm text-slate-500 mt-5">
                    Favorilemek, okuma listenize eklemek veya satın almak için
                    <a href="{{ route('login') }}" class="text-slate-900 underline">giriş yapın</a>.
                </p>
            @endauth
        </div>
    </div>
@endsection
