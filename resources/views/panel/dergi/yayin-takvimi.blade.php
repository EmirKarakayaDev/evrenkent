@extends('layouts.panel')

@section('title', 'Yayın Takvimi')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Yayın Takvimi</h1>

    @if ($issues->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-calendar class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Henüz bir sayınız yok.
        </div>
    @else
        <div class="card divide-y divide-slate-100">
            @foreach ($issues as $issue)
                <div class="flex items-center justify-between gap-3 px-5 py-4">
                    <div class="min-w-0">
                        <div class="font-medium text-slate-900 truncate">{{ $issue->title }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">
                            {{ $issue->publish_date ? $issue->publish_date->format('d.m.Y') : 'Tarih belirlenmedi' }}
                        </div>
                    </div>
                    <x-status-badge :status="$issue->status" />
                </div>
            @endforeach
        </div>
    @endif
@endsection
