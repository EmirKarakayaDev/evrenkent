@extends('layouts.public')

@section('title', $issue->title)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
        <x-magazine-cover :issue="$issue" class="aspect-[3/4] rounded-lg border border-slate-200 shadow-sm" icon-class="w-10 h-10" />

        <div class="min-w-0">
            <x-detail-header
                :title="$issue->title"
                :byline="$issue->editor->name"
                :meta="['Sayı ' . $issue->issue_number, $issue->publish_date?->translatedFormat('d M Y')]"
            >
                <x-status-badge :status="$issue->status" />
            </x-detail-header>

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
