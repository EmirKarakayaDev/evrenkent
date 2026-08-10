@extends('layouts.panel')

@section('title', 'Okuduklarım')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Okuduklarım</h1>

    @php
        $showComplete = false;
        $showReopen = true;
        $emptyMessage = 'Henüz tamamladığınız bir eser yok.';
    @endphp

    @include('panel.okuma-listesi._liste')
@endsection
