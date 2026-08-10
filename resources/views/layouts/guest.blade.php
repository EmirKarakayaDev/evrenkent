<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Evrenkent') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-orange-50 px-6 py-12">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 mb-8">
                <span class="flex items-center justify-center w-9 h-9 rounded-full bg-slate-900 text-white">
                    <x-heroicon-o-book-open class="w-4 h-4" />
                </span>
                <span class="font-serif text-xl font-semibold tracking-tight text-slate-900">Evrenkent</span>
            </a>

            <div class="w-full sm:max-w-md bg-white border border-slate-200 rounded-lg px-8 py-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
