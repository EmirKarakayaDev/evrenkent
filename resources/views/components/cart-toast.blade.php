{{--
    Sepete ekleme geri bildirimi — Hepsiburada tarzı: sağ üstte kısa süreli bir toast,
    "Sepete Git →" linkiyle. Alpine.store('cart').showToast() tarafından tetiklenir
    (bkz. add-to-cart-button.blade.php, resources/js/app.js).
--}}
<div
    x-data
    x-show="$store.cart.toast.visible"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-end="opacity-0"
    class="fixed top-20 right-6 z-30 w-80 card p-4 flex items-start gap-3"
>
    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 shrink-0">
        <x-heroicon-o-check class="w-5 h-5" />
    </span>
    <div class="min-w-0">
        <div class="text-sm font-medium text-slate-900 truncate" x-text="$store.cart.toast.title"></div>
        <div class="text-xs text-slate-400 mt-0.5">Sepete eklendi</div>
        <a :href="$store.cart.toast.url" class="text-xs font-medium text-brand-600 hover:text-brand-700 mt-1 inline-block">Sepete Git →</a>
    </div>
    <button type="button" title="Kapat" @click="$store.cart.toast.visible = false" class="ms-auto text-slate-400 hover:text-slate-600 shrink-0">
        <x-heroicon-o-x-mark class="w-4 h-4" />
    </button>
</div>
