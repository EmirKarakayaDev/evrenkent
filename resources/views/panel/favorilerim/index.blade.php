@extends('layouts.panel')

@section('title', 'Favorilerim')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Favorilerim</h1>

    @include('panel.favorilerim._liste')
@endsection
