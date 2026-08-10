@extends('layouts.panel')

@section('title', 'Okuma Listem')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Okuma Listem</h1>

    @php
        $showComplete = true;
        $showReopen = false;
        $emptyMessage = 'Okuma listeniz şu an boş.';
    @endphp

    @include('panel.okuma-listesi._liste')
@endsection
