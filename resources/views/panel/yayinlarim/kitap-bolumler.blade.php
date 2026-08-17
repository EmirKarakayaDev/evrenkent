@extends('layouts.panel')

@section('title', 'Bölümler')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
        <div class="min-w-0">
            <h1 class="font-serif text-xl font-semibold text-slate-900 break-words">{{ $book->title }} — Bölümler</h1>
            <a href="{{ route('panel.yayinlarim.kitap.duzenle', $book) }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">
                &larr; Kitaba dön
            </a>
        </div>
        <a href="{{ route('panel.yayinlarim.kitap.bolumler.yeni', $book) }}" class="text-sm px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors shrink-0">
            Yeni Bölüm
        </a>
    </div>

    @if ($chapters->isEmpty())
        <div class="bg-white border border-slate-200 rounded-lg p-12 text-center text-slate-400">
            <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Bu kitap için henüz bölüm eklenmedi.
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-lg divide-y divide-slate-100">
            @foreach ($chapters as $chapter)
                <div class="flex items-center justify-between gap-3 px-5 py-4 flex-wrap sm:flex-nowrap">
                    <div class="min-w-0">
                        <span class="text-xs uppercase text-orange-700 font-medium tracking-wide">Bölüm {{ $chapter->order }}</span>
                        <div class="font-medium text-slate-900 truncate">{{ $chapter->title }}</div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        <a href="{{ route('panel.yayinlarim.kitap.bolumler.duzenle', [$book, $chapter]) }}" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            Düzenle
                        </a>
                        <form method="POST" action="{{ route('panel.yayinlarim.kitap.bolumler.sil', [$book, $chapter]) }}" onsubmit="return confirm('Bu bölümü silmek istediğinize emin misiniz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                                Sil
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
