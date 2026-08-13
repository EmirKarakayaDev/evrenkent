@if ($favorites->isEmpty())
    <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
        <x-heroicon-o-heart class="w-8 h-8 mx-auto mb-3 text-slate-300" />
        Henüz favorilere eklediğiniz bir eser yok.
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
        @foreach ($favorites as $favorite)
            <div class="flex items-center justify-between px-5 py-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-book-open class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                    <div>
                        <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">Kitap</span>
                        <div class="font-medium text-slate-900">{{ $favorite->favoritable?->title ?? 'Silinmiş içerik' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if ($favorite->favoritable instanceof \App\Models\Book)
                        <a href="{{ route('kitaplar.show', $favorite->favoritable) }}" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            Görüntüle
                        </a>
                    @endif
                    <form method="POST" action="{{ route('panel.favoriler.sil', $favorite) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            Favoriden Çıkar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
