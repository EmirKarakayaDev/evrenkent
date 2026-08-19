@extends('layouts.admin-panel')

@section('title', 'Kategoriler')

@section('content')
    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <div>
            <h1 class="font-serif text-xl font-semibold text-slate-900">Kategoriler</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $categories->total() }} kategori.</p>
        </div>
        <a href="{{ route('panel.adminpanel.kategoriler.yeni') }}" class="btn-brand btn-sm">
            <x-heroicon-o-plus class="w-4 h-4" /> Yeni Kategori
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left text-xs text-slate-400 uppercase tracking-wide">
                        <th class="px-5 py-3 font-medium">Ad</th>
                        <th class="px-5 py-3 font-medium">Slug</th>
                        <th class="px-5 py-3 font-medium">Kitap</th>
                        <th class="px-5 py-3 font-medium">Makale</th>
                        <th class="px-5 py-3 font-medium text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 text-slate-900">{{ $category->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $category->slug }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $category->books_count }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $category->articles_count }}</td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('panel.adminpanel.kategoriler.duzenle', $category) }}" class="inline-flex items-center gap-1.5 text-sm text-brand-700 hover:text-brand-800 transition-colors">
                                        <x-heroicon-o-pencil class="w-4 h-4" /> Düzenle
                                    </a>
                                    <form method="POST" action="{{ route('panel.adminpanel.kategoriler.sil', $category) }}" data-turbo-confirm="&quot;{{ $category->name }}&quot; kalıcı olarak silinecek, bu kategoriye bağlı kitap/makale eşleşmeleri de kalkacak. Emin misiniz?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 transition-colors">
                                            <x-heroicon-o-trash class="w-4 h-4" /> Sil
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">Kategori bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $categories->links() }}
    </div>
@endsection
