@extends('layouts.panel')

@section('title', $title)

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="font-serif text-xl font-semibold text-slate-900">{{ $title }}</h1>
        <a href="{{ route('panel.yayinlarim.taslaklarim.yeni') }}" class="inline-flex items-center gap-1.5 text-sm px-4 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-colors">
            <x-heroicon-o-plus class="w-4 h-4" />
            Yeni Taslak Oluştur
        </a>
    </div>

    @if (session('status'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-md px-4 py-2.5">
            {{ session('status') }}
        </div>
    @endif

    @include('panel.yayinlarim._liste')
@endsection
