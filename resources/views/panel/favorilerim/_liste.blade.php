@if ($favorites->isEmpty())
    <div class="card p-12 text-center text-slate-400">
        <x-heroicon-o-heart class="w-8 h-8 mx-auto mb-3 text-slate-300" />
        Henüz favorilere eklediğiniz bir eser yok.
    </div>
@else
    <div class="card divide-y divide-slate-100">
        @foreach ($favorites as $favorite)
            <div class="flex items-center justify-between gap-3 px-5 py-4 flex-wrap sm:flex-nowrap">
                @if ($favorite->favoritable)
                    <a href="{{ $favorite->favoritable->url() }}" class="group flex items-start gap-3 min-w-0">
                        <x-heroicon-o-book-open class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                        <div class="min-w-0">
                            <span class="text-xs uppercase text-brand-700 font-medium tracking-wide">Kitap</span>
                            <div class="font-medium text-slate-900 truncate group-hover:underline">{{ $favorite->favoritable->title }}</div>
                        </div>
                    </a>
                @else
                    <div class="flex items-start gap-3 min-w-0">
                        <x-heroicon-o-book-open class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                        <div class="min-w-0">
                            <span class="text-xs uppercase text-brand-700 font-medium tracking-wide">Kitap</span>
                            <div class="font-medium text-slate-900 truncate">Silinmiş içerik</div>
                        </div>
                    </div>
                @endif
                <div class="flex items-center gap-3 shrink-0">
                    @if ($favorite->favoritable)
                        <a href="{{ $favorite->favoritable->url() }}" class="btn-outline btn-sm">
                            Görüntüle
                        </a>
                    @endif
                    <form method="POST" action="{{ route('panel.favoriler.sil', $favorite) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-outline btn-sm">
                            Favoriden Çıkar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
