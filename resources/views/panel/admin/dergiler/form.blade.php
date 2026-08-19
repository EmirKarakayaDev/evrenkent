@extends('layouts.admin-panel')

@section('title', $issue ? 'Sayıyı Düzenle' : 'Yeni Sayı')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
            <h1 class="font-serif text-xl font-semibold text-slate-900">{{ $issue ? 'Sayıyı Düzenle' : 'Yeni Sayı' }}</h1>
            @if ($issue)
                <x-status-badge :status="$issue->status" />
            @endif
        </div>

        <form
            method="POST"
            action="{{ $issue ? route('panel.adminpanel.dergiler.guncelle', $issue) : route('panel.adminpanel.dergiler.store') }}"
            enctype="multipart/form-data"
            class="card p-6 space-y-5"
        >
            @csrf
            @if ($issue)
                @method('PUT')
            @endif

            <div>
                <label for="editor_id" class="block text-sm font-medium text-slate-700 mb-1">Dergi Editörü</label>
                <select id="editor_id" name="editor_id" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach ($editors as $editor)
                        <option value="{{ $editor->id }}" @selected(old('editor_id', $issue?->editor_id) == $editor->id)>{{ $editor->name }}</option>
                    @endforeach
                </select>
                @error('editor_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Başlık</label>
                <input id="title" name="title" type="text" value="{{ old('title', $issue?->title) }}" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="issue_number" class="block text-sm font-medium text-slate-700 mb-1">Sayı No</label>
                <input id="issue_number" name="issue_number" type="number" min="1" value="{{ old('issue_number', $issue?->issue_number) }}" class="w-full max-w-xs rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                @error('issue_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="cover_image" class="block text-sm font-medium text-slate-700 mb-1">Kapak Görseli</label>
                @if ($issue?->cover_image)
                    <x-magazine-cover :issue="$issue" class="w-24 aspect-[3/4] rounded-md mb-2" />
                @endif
                <input id="cover_image" name="cover_image" type="file" accept="image/*" class="w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border file:border-slate-300 file:bg-white file:text-sm file:text-slate-700 hover:file:bg-slate-50">
                <p class="text-xs text-slate-400 mt-1">{{ $issue ? 'Değiştirmek istemiyorsanız boş bırakın.' : 'Opsiyonel, en fazla 5MB.' }}</p>
                @error('cover_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="editor_note" class="block text-sm font-medium text-slate-700 mb-1">Editör Yazısı</label>
                <textarea id="editor_note" name="editor_note" rows="6" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">{{ old('editor_note', $issue?->editor_note) }}</textarea>
                @error('editor_note') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            @if ($issue)
                <div>
                    <div class="block text-sm font-medium text-slate-700 mb-1">Durum</div>
                    <p class="text-xs text-slate-400">Sadece <a href="{{ route('panel.adminpanel.onaylar.index', ['tur' => 'dergiler']) }}" class="underline">İçerik Onayları</a>'ndaki Onayla/Reddet/Yayınla aksiyonlarıyla değişir.</p>
                </div>
            @else
                <div>
                    <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Durum</label>
                    <select id="status" name="status" class="w-full rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                        @foreach (\App\Enums\ContentStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', 'taslak') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Oluşturduktan sonra durum sadece İçerik Onayları'ndaki aksiyonlarla değişir.</p>
                    @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label for="publish_date" class="block text-sm font-medium text-slate-700 mb-1">Yayın Tarihi (opsiyonel)</label>
                <input id="publish_date" name="publish_date" type="date" value="{{ old('publish_date', $issue?->publish_date?->format('Y-m-d')) }}" class="w-full max-w-xs rounded-md border-slate-300 text-sm focus:border-slate-500 focus:ring-slate-500">
                @error('publish_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4 pt-1">
                <button type="submit" class="btn-brand">{{ $issue ? 'Değişiklikleri Kaydet' : 'Sayıyı Oluştur' }}</button>
                <a href="{{ route('panel.adminpanel.dergiler.index') }}" class="text-sm text-slate-500 hover:text-slate-900 transition-colors">Vazgeç</a>
            </div>
        </form>
    </div>
@endsection
