@props(['noteableType', 'noteableId', 'defaultLocation' => null])

<form method="POST" action="{{ route('panel.notlar.ekle') }}" x-data="{ type: 'not' }" class="bg-white border border-slate-200 rounded-lg p-5 space-y-4">
    @csrf
    <input type="hidden" name="noteable_type" value="{{ $noteableType }}">
    <input type="hidden" name="noteable_id" value="{{ $noteableId }}">

    <div class="flex items-center gap-5">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="radio" name="type" value="not" x-model="type" checked class="text-slate-900 focus:ring-slate-500"> Not
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="radio" name="type" value="alinti" x-model="type" class="text-slate-900 focus:ring-slate-500"> Alıntı
        </label>
    </div>

    <div x-show="type === 'alinti'">
        <label for="location" class="block text-sm font-medium text-slate-700 mb-1">Sayfa / Bölüm</label>
        <input id="location" name="location" type="text" value="{{ old('location', $defaultLocation) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
    </div>

    <div>
        <textarea name="content" rows="3" placeholder="Not veya alıntı ekle..." class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">{{ old('content') }}</textarea>
        @error('content') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="text-sm px-4 py-2 bg-slate-900 text-white rounded-md hover:bg-slate-800 transition-colors">
        Kaydet
    </button>
</form>
