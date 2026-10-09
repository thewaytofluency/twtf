<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\ImpactItem;
use Illuminate\Database\Seeder;

/**
 * Extra landing page content on top of the three defaults, so the landing page looks like a
 * real academy's (and shows a 6-course grid and a 4-card impact row). Keyed on title, so re-runs
 * are harmless.
 */
class DemoLandingSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([CourseSeeder::class, ImpactItemSeeder::class]);

        $courses = [
            ['Business English', 'Emails, meetings, presentations and negotiations: the English you need at work.', '💼', 'amber'],
            ['IELTS & TOEFL Preparation', 'Exam strategies, timed practice and feedback for the four skills.', '🎓', 'rose'],
            ['Conversation Club', 'Weekly guided speaking practice to build confidence and fluency.', '💬', 'teal'],
        ];

        foreach ($courses as [$title, $description, $emoji, $accent]) {
            Course::firstOrCreate(['title' => $title], [
                'description' => $description,
                'emoji' => $emoji,
                'accent' => $accent,
                'sort_order' => Course::nextSortOrder(),
            ]);
        }

        $impact = [
            ['2,500+', 'Learners Taught', 'Students across Mozambique and abroad have improved their English with us.', '🎓'],
        ];

        foreach ($impact as [$stat, $title, $description, $emoji]) {
            ImpactItem::firstOrCreate(['title' => $title], [
                'stat' => $stat,
                'description' => $description,
                'emoji' => $emoji,
                'sort_order' => ImpactItem::nextSortOrder(),
            ]);
        }

        // Put figures on the original three too, so the cards read as proof, not slogans.
        foreach ([
            'Empowering Communities' => '40+',
            'Transforming Lives' => '1,200+',
            'Global Reach' => '15+',
        ] as $title => $stat) {
            ImpactItem::where('title', $title)->whereNull('stat')->update(['stat' => $stat]);
        }
    }
}
