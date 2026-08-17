@extends('layouts.panel')

@section('title', 'Sayılarım')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Sayılarım</h1>

    <div class="flex flex-wrap gap-2.5 mb-8">
        <a href="{{ route('panel.dergi.sayilarim') }}" class="{{ ! $status ? 'pill-active' : 'pill-idle' }}">Tümü</a>
        @foreach (\App\Enums\ContentStatus::cases() as $case)
            <a href="{{ route('panel.dergi.sayilarim', ['durum' => $case->value]) }}" class="{{ $status === $case ? 'pill-active' : 'pill-idle' }}">
                {{ $case->label() }}
            </a>
        @endforeach
    </div>

    @if ($issues->isEmpty())
        <div class="card p-12 text-center text-slate-400">
            <x-heroicon-o-newspaper class="w-8 h-8 mx-auto mb-3 text-slate-300" />
            Bu durumda bir sayınız yok.
        </div>
    @else
        <div class="card divide-y divide-slate-100">
            @foreach ($issues as $issue)
                <div class="flex items-center justify-between gap-3 px-5 py-4 flex-wrap sm:flex-nowrap">
                    <div class="flex items-start gap-3 min-w-0">
                        <x-heroicon-o-newspaper class="w-5 h-5 text-slate-300 mt-0.5 shrink-0" />
                        <div class="min-w-0">
                            <div class="font-medium text-slate-900 truncate">{{ $issue->title }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">Sayı {{ $issue->issue_number }}</div>
                            <x-status-badge :status="$issue->status" class="mt-1.5" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        <a href="{{ route('dergiler.show', $issue) }}" class="btn-outline btn-sm">Görüntüle</a>
                        <a href="{{ \App\Filament\Resources\MagazineIssueResource::getUrl('edit', ['record' => $issue]) }}" class="btn-dark btn-sm">Düzenle</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
