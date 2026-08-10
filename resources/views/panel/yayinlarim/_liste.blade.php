@if ($books->isEmpty() && $articles->isEmpty())
    <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
        <x-heroicon-o-document-text class="w-8 h-8 mx-auto mb-3 text-slate-300" />
        Bu kategoride bir yayın yok.
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
        @foreach ($books as $book)
            <div class="flex items-center justify-between px-5 py-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-book-open class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                    <div>
                        <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">Kitap</span>
                        <div class="font-medium text-slate-900">{{ $book->title }}</div>
                        <x-status-badge :status="$book->status" class="mt-1.5" />
                        @if ($book->status === \App\Enums\ContentStatus::RevizyonIstendi && $book->reviews->first()?->note)
                            <p class="text-sm text-orange-800 bg-orange-50 border border-orange-200 rounded-md px-3 py-1.5 mt-2 max-w-md">
                                <span class="font-medium">Süper Admin notu:</span> {{ $book->reviews->first()->note }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @can('update', $book)
                        <a href="{{ route('panel.yayinlarim.kitap.duzenle', $book) }}" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50 transition-colors">
                            Düzenle
                        </a>
                    @endcan
                    @if ($showActions ?? false)
                        <form method="POST" action="{{ route('panel.yayinlarim.kitap.gonder', $book) }}">
                            @csrf
                            <button type="submit" class="text-sm px-3.5 py-1.5 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-colors">
                                Gönder
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach

        @foreach ($articles as $article)
            <div class="flex items-center justify-between px-5 py-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-document-text class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                    <div>
                        <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">Makale</span>
                        <div class="font-medium text-slate-900">{{ $article->title }}</div>
                        <x-status-badge :status="$article->status" class="mt-1.5" />
                        @if ($article->status === \App\Enums\ContentStatus::RevizyonIstendi && $article->reviews->first()?->note)
                            <p class="text-sm text-orange-800 bg-orange-50 border border-orange-200 rounded-md px-3 py-1.5 mt-2 max-w-md">
                                <span class="font-medium">Not:</span> {{ $article->reviews->first()->note }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @can('update', $article)
                        <a href="{{ route('panel.yayinlarim.makale.duzenle', $article) }}" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50 transition-colors">
                            Düzenle
                        </a>
                    @endcan
                    @if ($showActions ?? false)
                        <form method="POST" action="{{ route('panel.yayinlarim.makale.gonder', $article) }}">
                            @csrf
                            <button type="submit" class="text-sm px-3.5 py-1.5 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-colors">
                                Gönder
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
