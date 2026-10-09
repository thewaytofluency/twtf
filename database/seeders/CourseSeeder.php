<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * The three courses the landing page originally hard-coded. Only seeds an empty table, so
     * re-running never resurrects courses the admin deleted or renamed.
     */
    public function run(): void
    {
        if (Course::exists()) {
            return;
        }

        $courses = [
            ['Beginner English', 'Start your journey with basic English lessons.', '🌱', 'green'],
            ['Intermediate English', 'Enhance your skills with intermediate-level content.', '📈', 'blue'],
            ['Advanced English', 'Master English with advanced lessons and tips.', '🏆', 'purple'],
        ];

        foreach ($courses as $i => [$title, $description, $emoji, $accent]) {
            Course::create([
                'title' => $title,
                'description' => $description,
                'emoji' => $emoji,
                'accent' => $accent,
                'sort_order' => $i + 1,
            ]);
        }
    }
}
