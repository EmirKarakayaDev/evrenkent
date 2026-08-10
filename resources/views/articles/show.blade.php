@extends('layouts.public')

@section('title', $article->title)

@section('content')
    <div class="max-w-3xl">
        <div class="text-xs text-orange-700 font-medium uppercase tracking-wide">{{ $article->author->name }}</div>
        <h1 class="font-serif text-2xl font-semibold text-slate-900 mt-1">{{ $article->title }}</h1>

        <div class="flex items-center gap-2 mt-2">
            <x-status-badge :status="$article->status" />
            @if ($article->magazineIssue)
                <span class="text-sm text-slate-500">{{ $article->magazineIssue->title }}</span>
            @endif
        </div>

        <div class="text-slate-700 leading-relaxed whitespace-pre-line mt-6">{{ $article->content }}</div>

        @auth
            <div class="mt-10">
                <h2 class="font-serif text-lg font-semibold text-slate-900 mb-3">Not / Alıntı Ekle</h2>
                <x-quick-note-form
                    :noteable-type="\App\Models\Article::class"
                    :noteable-id="$article->id"
                />
            </div>
        @else
            <p class="text-sm text-slate-500 mt-8">
                Not veya alıntı eklemek için <a href="{{ route('login') }}" class="text-slate-900 underline">giriş yapın</a>.
            </p>
        @endauth
    </div>
@endsection
