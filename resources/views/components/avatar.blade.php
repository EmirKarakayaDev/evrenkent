@props(['name', 'id' => null])

@php
    // Kişiye göre tutarlı bir renk — id (veya isim) hash'lenip sabit bir paletten seçiliyor,
    // aynı kişi her yerde aynı renkte görünür (magazine-cover'daki gradyan seçim mantığıyla aynı desen).
    $palette = ['bg-indigo-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-sky-500', 'bg-violet-500', 'bg-teal-500', 'bg-orange-500'];
    $seed = $id ?? crc32($name);
    $color = $palette[$seed % count($palette)];

    $initials = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-xs font-semibold shrink-0 $color"]) }}>
    {{ mb_strtoupper($initials) }}
</span>
