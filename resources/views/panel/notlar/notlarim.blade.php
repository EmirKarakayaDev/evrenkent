@extends('layouts.panel')

@section('title', 'Notlarım')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Notlarım</h1>

    @include('panel.notlar._form', ['type' => \App\Enums\NoteType::Not])

    @php $emptyMessage = 'Henüz not almadınız.'; @endphp
    @include('panel.notlar._liste')
@endsection
