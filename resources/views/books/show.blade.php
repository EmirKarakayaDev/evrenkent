@extends('layouts.public')

@section('title', $book->title)

@section('content')
    @php
        $isAuthor = auth()->check() && auth()->id() === $book->author_id;
        $locked = $book->price > 0 && ! ($isAuthor || $hasPurchased);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr_320px] gap-8">
        <x-book-cover :book="$book" class="aspect-[3/4] rounded-lg border border-slate-200 shadow-sm" icon-class="w-10 h-10" />

        <div class="min-w-0">
            <div class="text-xs text-brand-600 font-medium uppercase tracking-wide">{{ $book->author->name }}</div>
            <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-slate-900 mt-1">{{ $book->title }}</h1>

            <div class="flex flex-wrap items-center gap-2 mt-3">
                <x-status-badge :status="$book->status" />
                @foreach ($book->categories as $category)
                    <span class="pill-tag !py-1 !px-3 !text-xs">{{ $category->name }}</span>
                @endforeach
            </div>

            {{-- Gerçek veriye dayanan istatistikler — mockup'taki sayfa/video/harita sayaçları yerine
                 elimizde olanı gösteriyoruz (yorum/puanlama sistemi henüz yok, uydurmuyoruz). --}}
            @if ($chapterCount > 0)
                <div class="flex items-center gap-2 mt-4 text-sm text-slate-500">
                    <x-heroicon-o-book-open class="w-4 h-4" />
                    {{ $chapterCount }} {{ $chapterCount === 1 ? 'bölüm' : 'bölüm' }}
                    @if ($book->published_at)
                        <span class="text-slate-300">·</span>
                        {{ $book->published_at->translatedFormat('d M Y') }}
                    @endif
                </div>
            @endif

            @if ($book->description)
                <div class="card p-5 mt-5">
                    <h2 class="font-serif text-base font-semibold text-slate-900 mb-2">Kitap Hakkında</h2>
                    <p class="text-slate-600 whitespace-pre-line leading-relaxed">{{ $book->description }}</p>
                </div>
            @endif
        </div>

        <div class="card p-5 h-fit lg:sticky lg:top-24">
            <div class="text-2xl font-serif font-semibold text-slate-900">{{ number_format($book->price, 2, ',', '.') }} TL</div>
            <div class="text-xs text-slate-400 mt-0.5">KDV dahil</div>

            <div class="flex flex-col gap-2.5 mt-4">
                @auth
                    @if ($hasPurchased)
                        <span class="btn bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 cursor-default">
                            <x-heroicon-o-check-circle class="w-4 h-4" /> Satın Alındı
                        </span>
                    @elseif ($book->status === \App\Enums\ContentStatus::Yayinda)
                        <form method="POST" action="{{ route('panel.satin-al', $book) }}">
                            @csrf
                            <button type="submit" class="btn-brand w-full">
                                <x-heroicon-o-shopping-bag class="w-4 h-4" /> Satın Al
                            </button>
                        </form>
                    @endif

                    @if ($book->status === \App\Enums\ContentStatus::Yayinda)
                        @if (! $locked)
                            <a href="{{ route('kitaplar.oku', $book) }}" class="btn-dark w-full">
                                <x-heroicon-o-book-open class="w-4 h-4" /> Oku
                            </a>
                        @endif
                    @endif

                    @if ($readingListItem && $readingListItem->status === \App\Enums\ReadingStatus::Tamamlandi)
                        <span class="btn-outline w-full cursor-default text-slate-400">
                            <x-heroicon-o-check class="w-4 h-4" /> Okundu
                        </span>
                    @elseif ($readingListItem)
                        <span class="btn-outline w-full cursor-default text-slate-400">
                            <x-heroicon-o-bookmark class="w-4 h-4" /> Okuma Listesinde
                        </span>
                    @else
                        <form method="POST" action="{{ route('panel.okuma-listesi.kitap.ekle', $book) }}">
                            @csrf
                            <button type="submit" class="btn-outline w-full">
                                <x-heroicon-o-bookmark class="w-4 h-4" /> Okuma Listeme Ekle
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('panel.favoriler.kitap.toggle', $book) }}">
                        @csrf
                        <button type="submit" class="btn-outline w-full">
                            <x-heroicon-o-heart class="w-4 h-4 {{ $hasFavorited ? 'text-brand-600' : '' }}" />
                            {{ $hasFavorited ? 'Favorilerde' : 'Favorilere Ekle' }}
                        </button>
                    </form>

                    @if ($hasPurchased || $book->status === \App\Enums\ContentStatus::Yayinda)
                        <p class="text-xs text-slate-400 mt-1 text-center">
                            Not/alıntı eklemek için <a href="{{ route('panel.notlarim') }}" class="underline hover:text-slate-600">Notlarım</a>'ı kullanabilirsiniz.
                        </p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-brand w-full">Giriş Yap ve Satın Al</a>
                    <p class="text-xs text-slate-400 text-center">
                        Favorilemek veya okuma listenize eklemek için de giriş yapmanız gerekiyor.
                    </p>
                @endauth
            </div>
        </div>
    </div>

    @if ($relatedBooks->isNotEmpty())
        <div class="mt-14">
            <div class="flex items-baseline justify-between mb-5">
                <h2 class="font-serif text-xl font-semibold text-slate-900">Bu Eserler de Dikkatini Çekebilir</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
                @foreach ($relatedBooks as $related)
                    <a href="{{ route('kitaplar.show', $related) }}" class="group block card-hover overflow-hidden">
                        <x-book-cover :book="$related" class="aspect-[3/4]" />
                        <div class="p-3">
                            <div class="text-xs text-brand-600 font-medium uppercase tracking-wide">{{ $related->author->name }}</div>
                            <div class="font-medium text-slate-900 text-sm truncate mt-0.5">{{ $related->title }}</div>
                            <div class="text-sm text-slate-500 mt-1">{{ number_format($related->price, 2, ',', '.') }} TL</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection
