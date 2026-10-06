<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocRequest;
use App\Models\Doc;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocController extends Controller
{
    public function index(): View
    {
        return view('admin.docs.index', [
            'docs' => Doc::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.docs.create', [
            'doc' => new Doc,
            'plans' => Plan::orderBy('access_level')->get(),
        ]);
    }

    public function store(DocRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('file');

        $file = $request->file('file');
        $data['file_path'] = $file->store('docs', 'local');
        $data['original_filename'] = $file->getClientOriginalName();
        $data['file_size'] = $file->getSize();
        $data['created_by'] = Auth::id();

        Doc::create($data);

        return redirect()->route('admin.docs.index')->with('status', 'Document created.');
    }

    public function edit(Doc $doc): View
    {
        return view('admin.docs.edit', [
            'doc' => $doc,
            'plans' => Plan::orderBy('access_level')->get(),
        ]);
    }

    public function update(DocRequest $request, Doc $doc): RedirectResponse
    {
        $data = $request->safe()->except('file');

        if ($request->hasFile('file')) {
            Storage::disk('local')->delete($doc->file_path);

            $file = $request->file('file');
            $data['file_path'] = $file->store('docs', 'local');
            $data['original_filename'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $doc->update($data);

        return redirect()->route('admin.docs.index')->with('status', 'Document updated.');
    }

    public function destroy(Doc $doc): RedirectResponse
    {
        Storage::disk('local')->delete($doc->file_path);
        $doc->delete();

        return redirect()->route('admin.docs.index')->with('status', 'Document deleted.');
    }
}
