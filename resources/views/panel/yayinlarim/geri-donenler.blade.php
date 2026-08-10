@extends('layouts.panel')

@section('title', $title)

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">{{ $title }}</h1>

    @if (session('status'))
        <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-md px-4 py-2.5">
            {{ session('status') }}
        </div>
    @endif

    @include('panel.yayinlarim._liste')
@endsection
