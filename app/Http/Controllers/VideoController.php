<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Support\LearningSummary;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $progress = $user->progressFor('video');
        $summary = new LearningSummary($user);

        $videos = Video::inSequence()->get();

        return view('videos.index', [
            'videosByLevel' => $videos->groupBy(fn (Video $video) => $video->course_level->value),
            'progress' => $progress,
            // Where to pick up: the video opened most recently that isn't finished, otherwise the
            // first accessible video not completed yet.
            'resume' => $summary->resumeVideo(),
            'resumeStarted' => ($r = $summary->resumeVideo()) ? $summary->hasStarted($r) : false,
        ]);
    }

    public function show(Video $video): View
    {
        $user = Auth::user();

        // isAccessibleBy() must be checked here, not just relied on via the index page's UI -
        // a user can navigate directly to this URL regardless of what the index page links to.
        abort_unless($video->isAccessibleBy($user), 403);

        $user->markViewed($video);

        $progress = $user->progressFor('video');
        $playlist = $video->playlist();

        return view('videos.show', [
            'video' => $video,
            'playlist' => $playlist,
            'progress' => $progress,
            'completedInPlaylist' => $playlist->filter(fn (Video $v) => $progress->get($v->id)?->completed_at)->count(),
            'completed' => (bool) $progress->get($video->id)?->completed_at,
            ...$video->neighbours(),
        ]);
    }
}
