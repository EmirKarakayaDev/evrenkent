@extends('layouts.public')

@section('title', 'Dergiler')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Dergiler</h1>

    @if ($issues->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-newspaper class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Henüz yayınlanmış bir dergi sayısı yok.
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
            @foreach ($issues as $issue)
                <a href="{{ route('dergiler.show', $issue) }}" class="group block card-hover overflow-hidden">
                    <x-magazine-cover :issue="$issue" class="aspect-[3/4]" />
                    <div class="p-3">
                        <div class="text-xs text-brand-600 font-medium uppercase tracking-wide">Sayı {{ $issue->issue_number }}</div>
                        <div class="font-medium text-slate-900 text-sm truncate mt-0.5">{{ $issue->title }}</div>
                        <div class="text-sm text-slate-500 mt-1">{{ $issue->articles_count }} {{ $issue->articles_count === 1 ? 'makale' : 'makale' }}</div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $issues->links() }}
        </div>
    @endif
@endsection
