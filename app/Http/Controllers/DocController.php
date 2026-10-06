<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocController extends Controller
{
    public function index(): View
    {
        return view('documents.index', [
            'docs' => Doc::orderBy('title')->get(),
        ]);
    }

    public function studyGuide(): View
    {
        $docs = Doc::whereNotNull('course_level')
            ->orderBy('title')
            ->get()
            ->groupBy(fn (Doc $doc) => $doc->course_level->value);

        return view('documents.study-guide', [
            'docsByLevel' => $docs,
        ]);
    }

    public function download(Doc $doc): StreamedResponse
    {
        // isAccessibleBy() must be checked here, not just relied on via the index page's UI —
        // a user can navigate directly to this URL regardless of what the index page links to.
        // Note: the 'local' disk's `serve => true` config (config/filesystems.php) auto-registers
        // a GET /storage/{path} route, but it requires a valid *signed* URL and nothing in this
        // app generates one for Doc::file_path — this download() call is the only real access
        // path to the file, and it's entirely in-process (streams via fpassthru, never redirects
        // to a URL), so this abort is the complete security boundary.
        abort_unless($doc->isAccessibleBy(Auth::user()), 403);

        return Storage::disk('local')->download($doc->file_path, $doc->original_filename);
    }
}
