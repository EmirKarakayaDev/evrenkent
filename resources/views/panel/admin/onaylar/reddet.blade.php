@extends('layouts.admin-panel')

@section('title', 'Reddet')

@section('content')
    <div class="max-w-lg mx-auto">
        <h1 class="font-serif text-xl font-semibold text-slate-900 mb-1">Reddet</h1>
        <p class="text-sm text-slate-500 mb-5">"{{ $title }}" revizyona geri gönderilecek, sahibi bir not göremezse gerekçeyi bilemez.</p>

        <form method="POST" action="{{ $submitRoute }}" class="card p-6 space-y-5">
            @csrf

            <div>
                <label for="note" class="block text-sm font-medium text-slate-700 mb-1">Revizyon Notu</label>
                <textarea id="note" name="note" rows="4" required class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">{{ old('note') }}</textarea>
                @error('note') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4 pt-1">
                <button type="submit" class="btn text-white bg-red-600 hover:bg-red-700 shadow-sm shadow-red-600/20">Reddet</button>
                <a href="{{ $backRoute }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">Vazgeç</a>
            </div>
        </form>
    </div>
@endsection
