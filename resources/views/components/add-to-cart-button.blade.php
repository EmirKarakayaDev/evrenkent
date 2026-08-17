@props(['book', 'inCart' => false])

{{--
    Sayfa yenilenmeden çalışır: tıklanınca fetch ile CartController::store'a gidiyor
    (Accept: application/json), başarılıysa header'daki sepet rozetini ($store.cart.count)
    ve "sepete eklendi" toast'ını ($store.cart.showToast) günceller, buton "Sepette"ye döner.
    JS kapalıysa/fetch başarısızsa normal form gönderimi (tam sayfa yenileme) devreye girer.
--}}
<div x-data="{ inCart: @js($inCart), loading: false }">
    <a x-show="inCart" x-cloak href="{{ route('panel.sepetim') }}" class="btn-outline-brand w-full">
        <x-heroicon-o-check class="w-4 h-4" /> Sepette
    </a>

    <form
        x-show="!inCart"
        method="POST"
        action="{{ route('panel.sepet.kitap.ekle', $book) }}"
        @submit.prevent="
            if (loading) return;
            loading = true;
            fetch($event.target.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            })
                .then(r => r.json())
                .then(data => {
                    loading = false;
                    if (data.added) {
                        inCart = true;
                        $store.cart.count = data.cartCount;
                        $store.cart.showToast(data.book.title, data.cartUrl);
                    }
                })
                .catch(() => { loading = false; $event.target.submit(); });
        "
    >
        @csrf
        <button type="submit" :disabled="loading" class="btn-outline-brand w-full" :class="{ 'opacity-60': loading }">
            <x-heroicon-o-shopping-cart class="w-4 h-4" /> Sepete Ekle
        </button>
    </form>
</div>
