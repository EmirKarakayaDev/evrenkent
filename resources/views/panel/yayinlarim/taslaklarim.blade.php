@extends('layouts.panel')

@section('title', $title)

@section('content')
    <div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
        <h1 class="font-serif text-xl font-semibold text-slate-900">{{ $title }}</h1>
        <a href="{{ route('panel.yayinlarim.taslaklarim.yeni') }}" class="btn-dark">
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
