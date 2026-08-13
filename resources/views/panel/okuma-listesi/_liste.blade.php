@if ($items->isEmpty())
    <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
        <x-heroicon-o-bookmark class="w-8 h-8 mx-auto mb-3 text-slate-300" />
        {{ $emptyMessage }}
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
        @foreach ($items as $item)
            <div class="flex items-center justify-between px-5 py-4">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-book-open class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                    <div>
                        <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">Kitap</span>
                        <div class="font-medium text-slate-900">{{ $item->readable?->title ?? 'Silinmiş içerik' }}</div>
                        @if ($item->completed_at)
                            <div class="text-xs text-slate-400 mt-1">Tamamlandı: {{ $item->completed_at->format('d.m.Y') }}</div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    @if ($showComplete ?? false)
                        <form method="POST" action="{{ route('panel.okuma-listesi.tamamla', $item) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm px-3.5 py-1.5 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
                                Tamamlandı Olarak İşaretle
                            </button>
                        </form>
                    @endif
                    @if ($showReopen ?? false)
                        <form method="POST" action="{{ route('panel.okuma-listesi.listeye-al', $item) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                                Listeye Geri Al
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('panel.okuma-listesi.sil', $item) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            Kaldır
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif
