@if ($notes->isEmpty())
    <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
        <x-heroicon-o-pencil-square class="w-8 h-8 mx-auto mb-3 text-slate-300" />
        {{ $emptyMessage }}
    </div>
@else
    <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
        @foreach ($notes as $note)
            <div class="flex items-start justify-between px-5 py-4 gap-4">
                <div class="min-w-0">
                    @if ($note->noteable)
                        <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">{{ $note->noteable->title }}</span>
                    @endif
                    @if ($note->title)
                        <div class="font-medium text-slate-900">{{ $note->title }}</div>
                    @endif
                    @if ($note->location)
                        <div class="text-xs text-slate-400 mt-0.5">{{ $note->location }}</div>
                    @endif
                    <p class="text-sm text-slate-600 mt-1.5 whitespace-pre-line">{{ $note->content }}</p>
                </div>
                <form method="POST" action="{{ route('panel.notlar.sil', $note) }}" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-md hover:bg-slate-50 transition-colors">
                        Sil
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif
