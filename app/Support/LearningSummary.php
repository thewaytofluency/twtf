<?php

namespace App\Support;

use App\Enums\CourseLevel;
use App\Models\Comment;
use App\Models\ContentProgress;
use App\Models\Doc;
use App\Models\Like;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Collection;

/**
 * Everything the "where am I / what next" parts of the app need to know about one student:
 * the video to resume, progress per level, totals and recent activity. Used by the home page,
 * the video list and the profile so they always agree.
 */
class LearningSummary
{
    private ?Collection $videos = null;

    private ?Collection $docs = null;

    private ?Collection $videoProgress = null;

    private ?Collection $docProgress = null;

    public function __construct(private User $user) {}

    /** The video opened most recently but not finished; otherwise the first unfinished, unlocked one. */
    public function resumeVideo(): ?Video
    {
        $progress = $this->videoProgress();

        $started = $this->videos()
            ->filter(fn (Video $v) => $v->isAccessibleBy($this->user)
                && $progress->get($v->id)?->viewed_at
                && ! $progress->get($v->id)?->completed_at)
            ->sortByDesc(fn (Video $v) => $progress->get($v->id)->viewed_at)
            ->first();

        return $started ?? $this->videos()->first(
            fn (Video $v) => $v->isAccessibleBy($this->user) && ! $progress->get($v->id)?->completed_at
        );
    }

    /** True when the resume video has actually been opened before (vs. "start learning"). */
    public function hasStarted(Video $video): bool
    {
        return (bool) $this->videoProgress()->get($video->id)?->viewed_at;
    }

    /** The first unfinished, unlocked document in path order. */
    public function nextDoc(): ?Doc
    {
        $progress = $this->docProgress();

        return $this->docs()->first(
            fn (Doc $d) => $d->isAccessibleBy($this->user) && ! $progress->get($d->id)?->completed_at
        );
    }

    /**
     * Per level: how many of the videos the student can open are completed.
     *
     * @return array<int, array{level: CourseLevel, done: int, total: int, percent: int}>
     */
    public function levelProgress(): array
    {
        $progress = $this->videoProgress();

        return collect(CourseLevel::cases())
            ->map(function (CourseLevel $level) use ($progress) {
                $open = $this->videos()->filter(
                    fn (Video $v) => $v->course_level === $level && $v->isAccessibleBy($this->user)
                );
                $done = $open->filter(fn (Video $v) => $progress->get($v->id)?->completed_at)->count();

                return [
                    'level' => $level,
                    'done' => $done,
                    'total' => $open->count(),
                    'percent' => $open->count() ? (int) round($done / $open->count() * 100) : 0,
                ];
            })
            ->filter(fn (array $row) => $row['total'] > 0)
            ->values()
            ->all();
    }

    /** @return array<string, int> */
    public function stats(): array
    {
        $videoProgress = $this->videoProgress();
        $docProgress = $this->docProgress();

        $openVideos = $this->videos()->filter(fn (Video $v) => $v->isAccessibleBy($this->user));
        $openDocs = $this->docs()->filter(fn (Doc $d) => $d->isAccessibleBy($this->user));

        return [
            'videosDone' => $openVideos->filter(fn (Video $v) => $videoProgress->get($v->id)?->completed_at)->count(),
            'videosTotal' => $openVideos->count(),
            'docsDone' => $openDocs->filter(fn (Doc $d) => $docProgress->get($d->id)?->completed_at)->count(),
            'docsTotal' => $openDocs->count(),
            'postsRead' => $this->user->contentProgress()->where('progressable_type', 'blog_post')->count(),
            'comments' => Comment::where('user_id', $this->user->id)->count(),
            'likes' => Like::where('user_id', $this->user->id)->count(),
        ];
    }

    /** Latest opened videos, documents and posts, newest first. */
    public function recent(int $limit = 6): Collection
    {
        return $this->user->contentProgress()
            ->whereNotNull('viewed_at')
            ->with('progressable')
            ->latest('viewed_at')
            ->limit($limit * 2)
            ->get()
            ->filter(fn (ContentProgress $row) => $row->progressable !== null)
            ->take($limit)
            ->values();
    }

    private function videos(): Collection
    {
        return $this->videos ??= Video::inSequence()->get();
    }

    private function docs(): Collection
    {
        return $this->docs ??= Doc::inSequence()->get();
    }

    private function videoProgress(): Collection
    {
        return $this->videoProgress ??= $this->user->progressFor('video');
    }

    private function docProgress(): Collection
    {
        return $this->docProgress ??= $this->user->progressFor('doc');
    }
}
