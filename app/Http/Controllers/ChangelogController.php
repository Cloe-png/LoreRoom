<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChangelogController extends Controller
{
    public function index(Request $request)
    {
        $entries = DB::table('changelog_entries')->where('is_published', true)->whereNotNull('published_at')->orderByDesc('published_at')->paginate(10);
        return view('manage.changelog.index', ['entries' => $entries, 'canManage' => $request->user()->isAdminOrChef()]);
    }

    public function create(Request $request) { $this->authorizeManager($request); return view('manage.changelog.form', ['entry' => null]); }
    public function edit(Request $request, int $entry) { $this->authorizeManager($request); return view('manage.changelog.form', ['entry' => $this->find($entry)]); }

    public function store(Request $request)
    {
        $this->authorizeManager($request);
        $data = $this->validateEntry($request);
        $data['body'] = $this->clean($data['body']);
        $data['created_at'] = $data['updated_at'] = now();
        $data['published_at'] = $data['is_published'] ? now() : null;
        DB::table('changelog_entries')->insert($data);
        return redirect()->route('manage.nouveautes.index')->with('success', 'Nouveauté enregistrée.');
    }

    public function update(Request $request, int $entry)
    {
        $this->authorizeManager($request);
        $row = $this->find($entry);
        $data = $this->validateEntry($request);
        $wasPublished = (bool) $row->is_published;
        $data['body'] = $this->clean($data['body']);
        $data['published_at'] = $data['is_published'] ? ($wasPublished ? $row->published_at : now()) : null;
        $data['updated_at'] = now();
        DB::table('changelog_entries')->where('id', $entry)->update($data);
        return redirect()->route('manage.nouveautes.index')->with('success', 'Nouveauté mise à jour.');
    }

    public function destroy(Request $request, int $entry)
    {
        $this->authorizeManager($request);
        DB::table('changelog_entries')->where('id', $entry)->delete();
        return redirect()->route('manage.nouveautes.index')->with('success', 'Nouveauté supprimée.');
    }

    public function seen(Request $request)
    {
        $latest = DB::table('changelog_entries')->where('is_published', true)->whereNotNull('published_at')->max('id');
        if ($latest) $request->user()->forceFill(['changelog_seen_id' => $latest])->save();
        return back();
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless($request->user()->isAdminOrChef(), 404);
    }

    private function find(int $id)
    {
        $entry = DB::table('changelog_entries')->where('id', $id)->first();
        abort_unless($entry, 404);
        return $entry;
    }

    private function validateEntry(Request $request): array
    {
        return $request->validate(['title' => ['required','string','max:80'], 'body' => ['required','string','max:280'], 'is_published' => ['nullable','boolean']]) + ['is_published' => $request->boolean('is_published')];
    }

    private function clean(string $body): string
    {
        $body = strip_tags($body, '<p><br><strong><b><em><i><ul><ol><li><a>');
        return preg_replace('/<(\/?)(p|br|strong|b|em|i|ul|ol|li|a)\b[^>]*>/i', '<$1$2>', $body) ?? Str::limit(strip_tags($body), 280, '');
    }
}
