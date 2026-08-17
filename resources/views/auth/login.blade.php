<x-guest-layout>
    <h1 class="font-serif text-2xl font-semibold text-slate-900 mb-6">Giriş Yap</h1>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="E-posta" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Şifre" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Beni hatırla</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-slate-500 hover:text-slate-900 transition-colors" href="{{ route('password.request') }}">
                    Şifremi unuttum
                </a>
            @endif
        </div>

        <div class="flex items-center justify-between gap-3 flex-wrap pt-2">
            <a href="{{ route('register') }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">
                Hesabın yok mu? Kayıt ol
            </a>

            <x-primary-button>
                Giriş Yap
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
