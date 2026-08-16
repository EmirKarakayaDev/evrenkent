{{--
    Kitap/Dergi Sayısı/Makale tanıtım sayfalarının ortak "üst blok" düzeni ve dikey
    ritmi — başlık → kişi adı → meta satırı → (varsa) değerlendirme → (varsa)
    istatistik şeridi → etiketler. Üçü de aynı sayfa hissini versin, aralıklar
    sayfadan sayfaya kayıp birbirinden sıkışık/ferah görünmesin diye tek yerden
    yönetiliyor — bkz. UI_RESTYLE_NOTES.md.
--}}
@props([
    'title',
    'byline' => null,
    'meta' => [],
    'ratingAverage' => null,
    'ratingCount' => null,
    'stats' => [],
])

<h1 class="font-serif text-2xl sm:text-3xl font-semibold text-slate-900">{{ $title }}</h1>

@if ($byline)
    <div class="text-sm text-brand-600 font-medium mt-2">{{ $byline }}</div>
@endif

@php $metaParts = array_filter($meta); @endphp
@if (count($metaParts) > 0)
    <div class="text-sm text-slate-500 mt-2">{{ implode('  ·  ', $metaParts) }}</div>
@endif

{{-- Değerlendirme — gerçek bir yorum/puanlama sistemi gelene kadar Süper Admin'in
     elle girdiği özet değer. Hiç girilmediyse hiç gösterilmiyor, sahte
     "0.0 (0 değerlendirme)" yazmıyoruz. --}}
@if ($ratingCount && $ratingAverage !== null)
    <div class="flex items-center gap-1.5 mt-4">
        <div class="flex items-center text-amber-400">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= round($ratingAverage))
                    <x-heroicon-s-star class="w-4 h-4" />
                @else
                    <x-heroicon-o-star class="w-4 h-4" />
                @endif
            @endfor
        </div>
        <span class="text-sm font-medium text-slate-900">{{ number_format($ratingAverage, 1) }}</span>
        <span class="text-sm text-slate-400">({{ $ratingCount }} değerlendirme)</span>
    </div>
@endif

@if (count($stats) > 0)
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mt-5 text-sm text-slate-500">
        @foreach ($stats as $stat)
            <span class="inline-flex items-center gap-1.5">
                <x-dynamic-component :component="$stat['icon']" class="w-4 h-4 text-slate-400" />
                {{ $stat['count'] }} {{ $stat['label'] }}
            </span>
        @endforeach
    </div>
@endif

@if (trim($slot) !== '')
    <div class="flex flex-wrap items-center gap-2 mt-5">
        {{ $slot }}
    </div>
@endif
