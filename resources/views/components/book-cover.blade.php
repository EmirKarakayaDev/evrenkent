@props(['book', 'iconClass' => 'w-7 h-7'])

@php
    $gradients = [
        'from-slate-700 to-slate-900',
        'from-orange-200 to-orange-400',
        'from-emerald-700 to-slate-900',
        'from-amber-100 to-orange-300',
        'from-sky-700 to-slate-900',
        'from-stone-300 to-stone-500',
    ];
    $gradient = $gradients[$book->id % count($gradients)];
@endphp

<div {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
    @if ($book->cover_image)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
    @else
        <div class="w-full h-full bg-gradient-to-br {{ $gradient }} flex items-center justify-center">
            <x-heroicon-o-book-open class="{{ $iconClass }} text-white/50" />
        </div>
    @endif
</div>
