@extends('layouts.admin-panel')

@section('title', 'Ana Sayfa')

@section('content')
    @php
        $maxSales = max($trend['sales']) ?: 1;
        $maxRevenue = max($trend['revenue']) ?: 1;
        $count = count($trend['labels']);

        $salesPoints = collect($trend['sales'])->map(function ($value, $i) use ($maxSales, $count) {
            $x = $count > 1 ? $i / ($count - 1) * 300 : 0;
            $y = 90 - ($value / $maxSales) * 80;

            return "{$x},{$y}";
        })->implode(' ');

        $revenuePoints = collect($trend['revenue'])->map(function ($value, $i) use ($maxRevenue, $count) {
            $x = $count > 1 ? $i / ($count - 1) * 300 : 0;
            $y = 90 - ($value / $maxRevenue) * 80;

            return "{$x},{$y}";
        })->implode(' ');
    @endphp

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <div>
            <h1 class="font-serif text-xl font-semibold text-slate-900">Hoş geldiniz, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-slate-500 mt-1">Evrenkent platformunun genel durumunu buradan takip edebilirsiniz.</p>
        </div>
        <div class="text-sm text-slate-500 border border-slate-200 rounded-lg px-3.5 py-2 bg-white">
            {{ now()->translatedFormat('d F Y, l') }}
        </div>
    </div>

    {{-- Stat kartları --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @foreach ($stats as $stat)
            <div class="card p-4">
                <div class="text-xs text-slate-500 mb-1.5">{{ $stat['label'] }}</div>
                <div class="text-xl font-semibold text-slate-900">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
        {{-- Bugünkü Durum --}}
        <div class="lg:col-span-3 card p-5">
            <div class="text-sm font-semibold text-slate-900 mb-4">Bugünkü Durum</div>
            <ul class="space-y-3">
                @foreach ($todayStatus as $item)
                    <li class="flex items-center gap-2.5 text-sm text-slate-600">
                        <span class="w-6 h-6 shrink-0 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-medium">{{ $item['count'] }}</span>
                        {{ $item['label'] }}
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Genel İstatistik Özeti --}}
        <div class="lg:col-span-5 card p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm font-semibold text-slate-900">Genel İstatistik Özeti</div>
                <span class="text-xs text-slate-400">Son 30 gün</span>
            </div>

            @if ($maxSales <= 1 && $maxRevenue <= 1)
                <p class="text-sm text-slate-400 py-10 text-center">Henüz yeterli satış verisi yok.</p>
            @else
                <svg viewBox="0 0 300 100" class="w-full h-32" preserveAspectRatio="none">
                    <polyline points="{{ $salesPoints }}" fill="none" stroke="#0ea5e9" stroke-width="2" vector-effect="non-scaling-stroke" />
                    <polyline points="{{ $revenuePoints }}" fill="none" stroke="#f59e0b" stroke-width="2" vector-effect="non-scaling-stroke" />
                </svg>
            @endif

            <div class="flex items-center gap-4 mt-3 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Satış (adet)</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Gelir (TL)</span>
            </div>
        </div>

        {{-- Canlı Akış --}}
        <div class="lg:col-span-4 card p-5">
            <div class="text-sm font-semibold text-slate-900 mb-4">Canlı Akış</div>
            @forelse ($activity as $event)
                <div class="flex items-start gap-2.5 py-2 {{ ! $loop->last ? 'border-b border-slate-100' : '' }}">
                    <span class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 shrink-0"></span>
                    <div class="min-w-0">
                        <div class="text-sm text-slate-700 truncate">{{ $event['title'] }}</div>
                        <div class="text-xs text-slate-400">{{ $event['subtitle'] }} · {{ $event['at']->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">Henüz bir hareket yok.</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- En Çok Satan Kitaplar --}}
        <div class="card p-5">
            <div class="text-sm font-semibold text-slate-900 mb-4">En Çok Satan Kitaplar</div>
            @forelse ($bestsellers as $row)
                <div class="flex items-center justify-between gap-2 py-2 {{ ! $loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="min-w-0">
                        <div class="text-sm text-slate-700 truncate">{{ $row->book->title }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ $row->book->author->name ?? '—' }}</div>
                    </div>
                    <span class="text-sm font-medium text-slate-900 shrink-0">{{ $row->sales_count }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Henüz satış yok.</p>
            @endforelse
        </div>

        {{-- Son Yayınlanan Dergi Sayıları --}}
        <div class="card p-5">
            <div class="text-sm font-semibold text-slate-900 mb-4">Son Yayınlanan Dergi Sayıları</div>
            @forelse ($recentIssues as $issue)
                <div class="flex items-center justify-between gap-2 py-2 {{ ! $loop->last ? 'border-b border-slate-100' : '' }}">
                    <div class="min-w-0">
                        <div class="text-sm text-slate-700 truncate">{{ $issue->title }}</div>
                        <div class="text-xs text-slate-400">Sayı {{ $issue->issue_number }}</div>
                    </div>
                    <span class="text-xs text-slate-400 shrink-0">{{ $issue->publish_date?->format('d.m.Y') }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400">Henüz yayınlanmış dergi sayısı yok.</p>
            @endforelse
        </div>

        {{-- Bekleyen Onaylar --}}
        <div id="bekleyen-onaylar" class="card p-5 scroll-mt-24">
            <div class="text-sm font-semibold text-slate-900 mb-4">Bekleyen Onaylar</div>
            @forelse ($pendingApprovals as $item)
                <a href="{{ $item['route'] }}" class="flex items-center justify-between gap-2 py-2.5 {{ ! $loop->last ? 'border-b border-slate-100' : '' }} hover:text-brand-700 transition-colors">
                    <span class="text-sm text-slate-700">{{ $item['label'] }}</span>
                    <span class="min-w-[1.5rem] h-6 px-1.5 rounded-full {{ $item['count'] > 0 ? 'bg-brand-50 text-brand-700' : 'bg-slate-100 text-slate-400' }} text-xs font-medium leading-6 text-center">{{ $item['count'] }}</span>
                </a>
            @empty
                <p class="text-sm text-slate-400">Bekleyen onay yok.</p>
            @endforelse
        </div>
    </div>
@endsection
