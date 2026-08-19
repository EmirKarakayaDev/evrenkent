<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Models\MagazineIssue;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Süper Admin'in Dergi Sayıları yönetimi — Filament'teki MagazineIssueResource'un
 * list/create/edit/delete'inin birebir aynısı, kendi panelimizde. MagazineIssueResource
 * silinmedi/değişmedi — bu, onunla paralel çalışan ikinci bir arayüz. (Faz 3 — bkz.
 * UI_RESTYLE_NOTES.md; AdminBookController'daki desenle birebir tutarlı.)
 */
class AdminMagazineIssueController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', MagazineIssue::class);

        $issues = MagazineIssue::query()
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.addcslashes($request->string('q'), '%_\\').'%'))
            ->when($request->filled('durum'), fn ($query) => $query->where('status', $request->string('durum')))
            ->with('editor')
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('panel.admin.dergiler.index', [
            'issues' => $issues,
            'q' => $request->string('q')->toString(),
            'durum' => $request->string('durum')->toString(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', MagazineIssue::class);

        return view('panel.admin.dergiler.form', [
            'issue' => null,
            'editors' => User::role('dergi_editoru')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', MagazineIssue::class);

        $data = $request->validate($this->validationRules(create: true));

        if ($request->hasFile('cover_image')) {
            // Filament'in FileUpload'ıyla aynı disk/dizin — x-magazine-cover bileşeni
            // ikisinde de aynı şekilde okuyor.
            $data['cover_image'] = $request->file('cover_image')->store('covers/magazine-issues', 'public');
        }

        $data['status'] = $data['status'] ?? ContentStatus::Taslak->value;

        $issue = MagazineIssue::create($data);

        return redirect()->route('panel.adminpanel.dergiler.duzenle', $issue)->with('status', 'Sayı oluşturuldu.');
    }

    public function edit(MagazineIssue $magazineIssue): View
    {
        $this->authorize('update', $magazineIssue);

        return view('panel.admin.dergiler.form', [
            'issue' => $magazineIssue,
            'editors' => User::role('dergi_editoru')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MagazineIssue $magazineIssue): RedirectResponse
    {
        $this->authorize('update', $magazineIssue);

        $data = $request->validate($this->validationRules(create: false));

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers/magazine-issues', 'public');
        } else {
            unset($data['cover_image']);
        }

        // Durum sadece İçerik Onayları akışıyla (Faz 1) değişir.
        unset($data['status']);

        $magazineIssue->update($data);

        return redirect()->route('panel.adminpanel.dergiler.duzenle', $magazineIssue)->with('status', 'Sayı güncellendi.');
    }

    public function destroy(MagazineIssue $magazineIssue): RedirectResponse
    {
        $this->authorize('delete', $magazineIssue);

        if ($magazineIssue->cover_image) {
            Storage::disk('public')->delete($magazineIssue->cover_image);
        }

        $magazineIssue->delete();

        return redirect()->route('panel.adminpanel.dergiler.index')->with('status', 'Sayı silindi.');
    }

    /**
     * @return array<string, array<mixed>>
     */
    private function validationRules(bool $create): array
    {
        return [
            'editor_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'issue_number' => ['required', 'integer', 'min:1'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'editor_note' => ['nullable', 'string'],
            'status' => $create ? ['required', 'in:'.implode(',', array_column(ContentStatus::cases(), 'value'))] : ['sometimes'],
            'publish_date' => ['nullable', 'date'],
        ];
    }
}
