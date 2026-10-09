<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\Doc;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

/**
 * Comments and likes from the demo students, so the blog and video pages look lived in.
 * Goes through the models (not raw inserts) so the like_count/comment_count observers run.
 */
class DemoEngagementSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42); // same "random" engagement every run

        $students = User::where('role', UserRole::Student)
            ->where('status', UserStatus::Active)
            ->where('email', 'like', '%@example.com')
            ->where('email', '!=', 'test@example.com')
            ->get();

        if ($students->isEmpty()) {
            return;
        }

        $this->comment($students);
        $this->like($students);
        $this->progress($students);
    }

    private function comment($students): void
    {
        // post slug => comments, in order; commenters are picked from the shuffled student pool.
        $threads = [
            'why-you-understand-english-but-cannot-speak-it' => [
                'This is exactly me! I understand everything in videos but freeze when someone asks me a question. Going to try talking to myself while cooking.',
                'The shadowing tip changed my week. Ten minutes a day and my pronunciation is already clearer.',
                'Thank you for saying that fear is the real problem. I always wait until my sentence is perfect and then the moment is gone.',
                'Do you recommend recording on the phone? I am embarrassed to hear my own voice 😅',
            ],
            '5-habits-of-learners-who-become-fluent' => [
                'Habit number 2 is a game changer. Learning "make a decision" instead of just "decision" made my sentences so much smoother.',
                'I started a mistakes notebook after reading this. Already found that I keep forgetting the s in third person singular!',
                'Football podcasts for me. I never thought I could enjoy listening practice this much.',
            ],
            'present-perfect-vs-past-simple-a-simple-way-to-choose' => [
                'The "I lived in Maputo for five years" vs "I have lived" example finally made it click. Thank you!',
                'Can we get a follow-up on present perfect continuous? That one still confuses me.',
                'In Portuguese we use the same tense for both, so this explanation was perfect for us.',
                'Wrote my five sentences like you suggested. Reading them aloud helped a lot.',
                'Great post. Is "I have seen him yesterday" really wrong?',
            ],
            'how-to-prepare-for-a-job-interview-in-english' => [
                'I have an interview next week and the STAR method is exactly what I needed. Thanks!',
                '"Could I take a moment to think about that?" - I never knew it sounded professional. I always panicked and stayed silent.',
                'Would love a post with sample answers for "what is your biggest weakness".',
            ],
            'phrasal-verbs-without-the-pain' => [
                'Separable vs inseparable was always a mystery to me. "Turn it off", never "turn off it" - got it!',
                'Learning them by topic works so much better than the alphabetical list I was using.',
                'Today I used "hand in" and "run out of" in real conversations at work. Small victories!',
            ],
            'building-your-listening-skills-a-4-week-plan' => [
                'Starting week 1 today. Will report back in a month!',
                'The transcript week is where I finally realised I knew most of the words, I just could not catch them at speed.',
            ],
            'welcome-to-the-way-to-fluency-how-your-plan-works' => [
                'Just subscribed to Standard. The approval was very fast, thank you!',
                'Is it possible to pay with e-Mola? Saw it in the text, just checking 🙂',
                'Love the platform so far. The study guide is really well organised.',
            ],
        ];

        foreach ($threads as $slug => $texts) {
            $post = BlogPost::where('slug', $slug)->first();

            if (! $post || $post->comments()->exists()) {
                continue;
            }

            $pool = $students->shuffle(); // mt_srand makes this deterministic
            foreach ($texts as $i => $text) {
                $comment = new Comment([
                    'blog_post_id' => $post->id,
                    'user_id' => $pool[$i % $pool->count()]->id,
                    'content' => $text,
                ]);
                $comment->created_at = $post->created_at->copy()->addHours(3 + $i * 9 + mt_rand(0, 6));
                $comment->save();
            }
        }
    }

    /** Study history, so profiles and the "continue where you left off" cards have something to show. */
    private function progress($students): void
    {
        mt_srand(11);

        $videos = Video::inSequence()->get();
        $docs = Doc::inSequence()->get();
        $posts = BlogPost::published()->get();

        foreach ($students as $i => $student) {
            // Each student is a different distance into the path: from just started to well along.
            $videoCount = [6, 4, 8, 3, 2, 10, 1, 0, 0, 1, 0, 2][$i % 12];
            $docCount = [3, 2, 4, 1, 1, 5, 0, 0, 0, 1, 0, 1][$i % 12];

            $openVideos = $videos->filter(fn (Video $v) => $v->isAccessibleBy($student))->values();
            $openDocs = $docs->filter(fn (Doc $d) => $d->isAccessibleBy($student))->values();

            foreach ($openVideos->take($videoCount) as $n => $video) {
                $this->record($student, $video, completed: true, daysAgo: max(1, $videoCount - $n) + mt_rand(0, 2));
            }
            // One lesson started but not finished: it becomes their "continue watching".
            if ($video = $openVideos->get($videoCount)) {
                $this->record($student, $video, completed: false, daysAgo: mt_rand(0, 1));
            }

            foreach ($openDocs->take($docCount) as $n => $doc) {
                $this->record($student, $doc, completed: true, daysAgo: max(1, $docCount - $n) + mt_rand(0, 3));
            }

            foreach ($posts->filter(fn () => mt_rand(0, 99) < 40) as $post) {
                $this->record($student, $post, completed: false, daysAgo: mt_rand(0, 20));
            }
        }
    }

    private function record(User $student, $item, bool $completed, int $daysAgo): void
    {
        $when = now()->subDays($daysAgo)->subMinutes(mt_rand(0, 600));

        $student->contentProgress()->updateOrCreate(
            ['progressable_type' => $item->getMorphClass(), 'progressable_id' => $item->getKey()],
            ['viewed_at' => $when, 'completed_at' => $completed ? $when : null],
        );
    }

    private function like($students): void
    {
        mt_srand(7); // independent of whether comment() consumed random numbers, so re-runs add nothing new

        $likeables = collect()
            ->concat(BlogPost::all())
            ->concat(Video::all())
            ->concat(Comment::all());

        foreach ($likeables as $item) {
            // Roughly a third to two thirds of students like each item; posts and videos a bit more than comments.
            $share = $item instanceof Comment ? 0.25 : mt_rand(35, 75) / 100;

            foreach ($students as $student) {
                if (mt_rand(0, 100) / 100 <= $share) {
                    $item->likes()->firstOrCreate(['user_id' => $student->id]);
                }
            }
        }
    }
}
