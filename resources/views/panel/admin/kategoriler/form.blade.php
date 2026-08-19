@extends('layouts.admin-panel')

@section('title', $category ? 'Kategoriyi Düzenle' : 'Yeni Kategori')

@section('content')
    <div class="max-w-lg mx-auto">
        <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">{{ $category ? 'Kategoriyi Düzenle' : 'Yeni Kategori' }}</h1>

        <form
            method="POST"
            action="{{ $category ? route('panel.adminpanel.kategoriler.guncelle', $category) : route('panel.adminpanel.kategoriler.store') }}"
            class="card p-6 space-y-5"
        >
            @csrf
            @if ($category)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Ad</label>
                <input id="name" name="name" type="text" value="{{ old('name', $category?->name) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $category?->slug) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                <p class="text-xs text-slate-400 mt-1">Kategori filtre linklerinde kullanılır, benzersiz olmalı.</p>
                @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4 pt-1">
                <button type="submit" class="btn-brand">{{ $category ? 'Değişiklikleri Kaydet' : 'Kategoriyi Oluştur' }}</button>
                <a href="{{ route('panel.adminpanel.kategoriler.index') }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">Vazgeç</a>
            </div>
        </form>
    </div>
@endsection
