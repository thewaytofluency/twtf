<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocController extends Controller
{
    public function index(): View
    {
        return view('documents.index', [
            'docs' => Doc::inSequence()->get(),
            'progress' => Auth::user()->progressFor('doc'),
        ]);
    }

    public function studyGuide(): View
    {
        $docs = Doc::whereNotNull('course_level')
            ->inSequence()
            ->get()
            ->groupBy(fn (Doc $doc) => $doc->course_level->value);

        return view('documents.study-guide', [
            'docsByLevel' => $docs,
            'progress' => Auth::user()->progressFor('doc'),
        ]);
    }

    public function show(Doc $doc): View
    {
        $user = Auth::user();

        // Same server-side gate as download(): the index page's locked/unlocked UI is not the boundary.
        abort_unless($doc->isAccessibleBy($user), 403);

        $user->markViewed($doc);

        $progress = $user->progressFor('doc');
        $playlist = $doc->playlist();

        return view('documents.show', [
            'doc' => $doc,
            'playlist' => $playlist,
            'progress' => $progress,
            'completedInPlaylist' => $playlist->filter(fn (Doc $d) => $progress->get($d->id)?->completed_at)->count(),
            'completed' => (bool) $progress->get($doc->id)?->completed_at,
            ...$doc->neighbours(),
        ]);
    }

    /** Streams the file inline for the in-page reader (PDF / image only). */
    public function preview(Doc $doc): Response
    {
        abort_unless($doc->isAccessibleBy(Auth::user()), 403);
        abort_unless($doc->isPreviewable(), 404);

        return Storage::disk('local')->response($doc->file_path, $doc->original_filename);
    }

    public function download(Doc $doc): StreamedResponse
    {
        $user = Auth::user();

        // isAccessibleBy() must be checked here, not just relied on via the index page's UI -
        // a user can navigate directly to this URL regardless of what the index page links to.
        // Note: the 'local' disk's `serve => true` config (config/filesystems.php) auto-registers
        // a GET /storage/{path} route, but it requires a valid *signed* URL and nothing in this
        // app generates one for Doc::file_path - this download() call is the only real access
        // path to the file, and it's entirely in-process (streams via fpassthru, never redirects
        // to a URL), so this abort is the complete security boundary.
        abort_unless($doc->isAccessibleBy($user), 403);

        // Downloading counts as having studied the document.
        $user->setCompleted($doc, true);

        return Storage::disk('local')->download($doc->file_path, $doc->original_filename);
    }
}
