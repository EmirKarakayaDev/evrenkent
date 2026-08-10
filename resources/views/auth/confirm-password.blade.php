<x-guest-layout>
    <h1 class="font-serif text-2xl font-semibold text-slate-900 mb-3">Şifreni Doğrula</h1>

    <div class="mb-6 text-sm text-slate-500">
        Bu, uygulamanın güvenli bir alanı. Devam etmeden önce şifreni doğrulaman gerekiyor.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="password" value="Şifre" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end pt-2">
            <x-primary-button>
                Doğrula
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
