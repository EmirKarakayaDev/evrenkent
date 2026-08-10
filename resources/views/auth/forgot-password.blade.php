<x-guest-layout>
    <h1 class="font-serif text-2xl font-semibold text-slate-900 mb-3">Şifremi Unuttum</h1>

    <div class="mb-6 text-sm text-slate-500">
        Sorun değil. E-posta adresini bırak, yeni bir şifre belirlemen için sana bir bağlantı gönderelim.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="E-posta" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end pt-2">
            <x-primary-button>
                Sıfırlama Bağlantısı Gönder
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
