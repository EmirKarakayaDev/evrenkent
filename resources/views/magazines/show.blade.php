@extends('layouts.public')

@section('title', $issue->title)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
        <x-magazine-cover :issue="$issue" class="aspect-[3/4] rounded-lg border border-slate-200 shadow-sm" icon-class="w-10 h-10" />

        <div class="min-w-0">
            <div class="text-xs text-brand-600 font-medium uppercase tracking-wide">Sayı {{ $issue->issue_number }}</div>
            <h1 class="font-serif text-2xl sm:text-3xl font-semibold text-slate-900 mt-1">{{ $issue->title }}</h1>

            <div class="flex flex-wrap items-center gap-2 mt-3">
                <x-status-badge :status="$issue->status" />
            </div>

            <div class="flex items-center gap-2 mt-4 text-sm text-slate-500">
                <x-heroicon-o-user class="w-4 h-4" />
                {{ $issue->editor->name }}
                @if ($issue->publish_date)
                    <span class="text-slate-300">·</span>
                    {{ $issue->publish_date->translatedFormat('d M Y') }}
                @endif
            </div>

            <div class="mt-8">
                <h2 class="font-serif text-base font-semibold text-slate-900 mb-3">Bu Sayıdaki Makaleler</h2>

                @if ($articles->isEmpty())
                    <div class="card p-8 text-center text-slate-400">
                        <x-heroicon-o-document-text class="w-8 h-8 mx-auto mb-3 text-slate-300" />
                        Bu sayıda henüz yayınlanmış bir makale yok.
                    </div>
                @else
                    <div class="card divide-y divide-slate-100">
                        @foreach ($articles as $article)
                            <a href="{{ route('makaleler.show', $article) }}" class="flex items-start gap-3 px-5 py-4 hover:bg-slate-50 transition-colors">
                                <x-heroicon-o-document-text class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                                <div class="min-w-0">
                                    <div class="font-medium text-slate-900">{{ $article->title }}</div>
                                    <div class="text-sm text-slate-500 mt-0.5">{{ $article->author->name }}</div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
