<x-guest-layout>
    <h1 class="font-serif text-2xl font-semibold text-slate-900 mb-3">E-postanı Doğrula</h1>

    <div class="mb-4 text-sm text-slate-500">
        Kayıt olduğun için teşekkürler! Başlamadan önce, sana gönderdiğimiz bağlantıya tıklayarak e-posta adresini doğrular mısın? E-postayı alamadıysan, sana memnuniyetle bir yenisini gönderelim.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-emerald-700">
            Kayıt sırasında verdiğin e-posta adresine yeni bir doğrulama bağlantısı gönderildi.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-3 flex-wrap">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                Doğrulama E-postasını Tekrar Gönder
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">
                Çıkış Yap
            </button>
        </form>
    </div>
</x-guest-layout>
