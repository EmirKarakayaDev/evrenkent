@extends('layouts.panel')

@section('title', $title)

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">{{ $title }}</h1>
    @include('panel.yayinlarim._liste')
@endsection
