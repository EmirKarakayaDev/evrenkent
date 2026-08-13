@extends('layouts.panel')

@section('title', 'Kitaplığım')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Kitaplığım</h1>

    @if ($items->isEmpty())
        <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
            <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Henüz kitaplığınıza eklenmiş bir eser yok.
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
            @foreach ($items as $item)
                <div class="flex items-center justify-between gap-4 px-5 py-4">
                    <div class="flex items-start gap-4 min-w-0">
                        <x-book-cover :book="$item->book" class="w-14 h-20 rounded-md shrink-0" icon-class="w-5 h-5" />
                        <div class="min-w-0">
                            @if ($item->book->categories->isNotEmpty())
                                <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">{{ $item->book->categories->first()->name }}</span>
                            @endif
                            <div class="font-medium text-slate-900 truncate">{{ $item->book->title }}</div>
                            <div class="text-sm text-slate-500">{{ $item->book->author->name }}</div>
                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                @if ($item->purchased)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">Satın Alındı</span>
                                @endif
                                @if ($item->favorited)
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-orange-50 text-orange-700 ring-orange-200">Favori</span>
                                @endif
                                @if ($item->readingItem)
                                    <x-status-badge :status="$item->readingItem->status" />
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="shrink-0">
                        @if ($item->readingItem?->status === \App\Enums\ReadingStatus::Listede)
                            <a href="{{ route('kitaplar.oku', $item->book) }}" class="text-sm px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                                Okumaya Devam Et
                            </a>
                        @elseif ($item->readingItem?->status === \App\Enums\ReadingStatus::Tamamlandi)
                            <a href="{{ route('kitaplar.oku', $item->book) }}" class="text-sm px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                                Tekrar Oku
                            </a>
                        @else
                            <a href="{{ route('kitaplar.show', $item->book) }}" class="text-sm px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                                Görüntüle
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
