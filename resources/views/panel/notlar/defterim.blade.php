@extends('layouts.panel')

@section('title', 'Defterim')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Defterim</h1>

    @include('panel.notlar._form', ['type' => \App\Enums\NoteType::Defter])

    @php $emptyMessage = 'Defteriniz şu an boş.'; @endphp
    @include('panel.notlar._liste')
@endsection
