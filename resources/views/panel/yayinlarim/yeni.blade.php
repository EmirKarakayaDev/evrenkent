@extends('layouts.panel')

@section('title', 'Yeni Taslak Oluştur')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Yeni Taslak Oluştur</h1>

    <form method="POST" action="{{ route('panel.yayinlarim.taslaklarim.store') }}" x-data="{ type: 'kitap' }" class="bg-white border border-slate-200 rounded-lg p-6 space-y-5 max-w-2xl">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tür</label>
            <div class="flex gap-5">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="radio" name="type" value="kitap" x-model="type" checked class="text-slate-900 focus:ring-slate-500"> Kitap
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="radio" name="type" value="makale" x-model="type" class="text-slate-900 focus:ring-slate-500"> Makale
                </label>
            </div>
            @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Başlık</label>
            <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
            @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="type === 'kitap'">
            <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Fiyat (TL)</label>
            <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', 0) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
            @error('price') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="body" class="block text-sm font-medium text-slate-700 mb-1">
                <span x-show="type === 'kitap'">Açıklama</span>
                <span x-show="type === 'makale'">İçerik</span>
            </label>
            <textarea id="body" name="body" rows="8" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">{{ old('body') }}</textarea>
            @error('body') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-1">
            <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                Taslak Olarak Kaydet
            </button>
            <a href="{{ route('panel.yayinlarim.taslaklarim') }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">
                Vazgeç
            </a>
        </div>
    </form>
@endsection
