@extends('layouts.panel')

@section('title', 'Alıntılarım')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Alıntılarım</h1>

    @include('panel.notlar._form', ['type' => \App\Enums\NoteType::Alinti])

    @php $emptyMessage = 'Henüz bir alıntı kaydetmediniz.'; @endphp
    @include('panel.notlar._liste')
@endsection
