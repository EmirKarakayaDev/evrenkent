@extends('layouts.panel')

@section('title', 'Kitabı Düzenle')

@section('content')
    <div class="flex items-center justify-between mb-5 max-w-2xl">
        <h1 class="font-serif text-xl font-semibold text-slate-900">Kitabı Düzenle</h1>
        <a href="{{ route('panel.yayinlarim.kitap.bolumler', $book) }}" class="text-sm px-3.5 py-1.5 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
            Bölümler
        </a>
    </div>

    <form method="POST" action="{{ route('panel.yayinlarim.kitap.guncelle', $book) }}" class="bg-white border border-slate-200 rounded-lg p-6 space-y-5 max-w-2xl">
        @csrf
        @method('PUT')

        <div>
            <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Başlık</label>
            <input id="title" name="title" type="text" value="{{ old('title', $book->title) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Fiyat (TL)</label>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $book->price) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
            @error('price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="body" class="block text-sm font-medium text-slate-700 mb-1">Açıklama</label>
            <textarea id="body" name="body" rows="8" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">{{ old('body', $book->description) }}</textarea>
            @error('body') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-1">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                Kaydet
            </button>
            <a href="{{ route('panel.yayinlarim.taslaklarim') }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">
                Vazgeç
            </a>
        </div>
    </form>
@endsection
