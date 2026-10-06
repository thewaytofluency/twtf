<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $videos = Video::orderBy('title')->get()->groupBy(fn (Video $video) => $video->course_level->value);

        return view('videos.index', [
            'videosByLevel' => $videos,
        ]);
    }

    public function show(Video $video): View
    {
        // isAccessibleBy() must be checked here, not just relied on via the index page's UI —
        // a user can navigate directly to this URL regardless of what the index page links to.
        abort_unless($video->isAccessibleBy(Auth::user()), 403);

        return view('videos.show', [
            'video' => $video,
        ]);
    }
}
