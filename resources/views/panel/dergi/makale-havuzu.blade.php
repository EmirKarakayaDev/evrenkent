@extends('layouts.panel')

@section('title', 'Makale Havuzu')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Makale Havuzu</h1>

    <div class="flex flex-wrap gap-2.5 mb-8">
        @foreach ($tabs as $key => $tab)
            <a href="{{ route('panel.dergi.makale-havuzu', ['durum' => $key]) }}" class="{{ $activeTab === $key ? 'pill-active' : 'pill-idle' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>

    @if ($articles->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-document-text class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Bu sekmede bir makale yok.
        </div>
    @else
        <div class="card divide-y divide-slate-100">
            @foreach ($articles as $article)
                <div class="flex items-center justify-between gap-3 px-5 py-4 flex-wrap sm:flex-nowrap">
                    <div class="min-w-0">
                        <div class="font-medium text-slate-900 truncate">{{ $article->title }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">
                            {{ $article->author->name }}
                            @if ($article->magazineIssue)
                                · {{ $article->magazineIssue->title }}
                            @endif
                            @if ($article->categories->isNotEmpty())
                                · {{ $article->categories->pluck('name')->join(', ') }}
                            @endif
                            · {{ $article->created_at->format('d.m.Y') }}
                        </div>
                        <x-status-badge :status="$article->status" class="mt-1.5" />
                    </div>
                    <a href="{{ \App\Filament\Resources\ArticleResource::getUrl('edit', ['record' => $article]) }}" class="btn-outline btn-sm shrink-0">
                        Görüntüle
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
