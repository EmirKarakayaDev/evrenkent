@extends('layouts.panel')

@section('title', $title)

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">{{ $title }}</h1>

    <div class="card p-12 text-center text-slate-400">
        <x-heroicon-o-book-open class="w-8 h-8 mx-auto mb-3 text-slate-300" />
        {{ $message }}
    </div>
@endsection
