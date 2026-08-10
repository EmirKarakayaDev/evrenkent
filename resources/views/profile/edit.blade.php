@extends('layouts.panel')

@section('title', 'Ayarlar')

@section('content')
    <h1 class="font-serif text-xl font-semibold text-slate-900 mb-5">Ayarlar</h1>

    <div class="space-y-6">
        <div class="p-6 bg-white border border-slate-200 rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 bg-white border border-slate-200 rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
