<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Seeder;

/**
 * Real, publicly available English-learning videos (BBC Learning English, English with Lucy,
 * Rachel's English). IDs were looked up and checked at the time of writing, but they live on
 * YouTube — if one is ever removed, only that card's player breaks.
 *
 * Access levels mirror PlanSeeder: 0 free, 1 Basic, 2 Standard, 3 Premium.
 */
class DemoVideosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        // [youtube id, title, description, level, required access level, published N days ago]
        $videos = [
            // — Beginner —
            ['8nXX1WOuvrk', 'What Is Your English Level? Take This Test', 'A quick, honest way to find out where you stand on the CEFR scale before choosing what to study. Do it once now and again in three months. From English with Lucy.', 'beginner', 0, 118],
            ['oUD2gUmdzeI', 'Spoken English Class 1: Speaking Practice From Beginner to Advanced', 'A full guided speaking class: listen, repeat and answer out loud. Pause often and say every sentence yourself. From English with Lucy.', 'beginner', 0, 110],
            ['QgjkjsqAzvo', 'How to Introduce Yourself in English', 'Self-introduction for interviews, classes and new friends, with ready-to-use phrases and what to avoid. From English with Lucy.', 'beginner', 0, 96],
            ['ryRu8zFCKZE', 'Articles: A, An, The and No Article', 'The article rules that cause the most mistakes for Portuguese speakers, explained in six minutes. From BBC Learning English (6 Minute Grammar).', 'beginner', 0, 84],
            ['N9B59PHIFbA', 'Make and Do: Which One Do You Use?', 'A 45-second reminder of the most common make/do collocations. Great as a daily warm-up. From BBC Learning English.', 'beginner', 1, 70],
            ['h8Q1XhjHhf4', 'Phrasal Verbs: 4 Helpful Hints and 10 Useful Examples', 'Why phrasal verbs feel impossible, and four habits that make them stick. Start here before the longer phrasal verb lessons. From English with Lucy.', 'beginner', 1, 62],

            // — Intermediate —
            ['YAsDeXcYyTg', 'Scared to Speak English? 6 Minute English', 'Why so many learners freeze when speaking, and what actually helps. Includes vocabulary and a transcript on the BBC site. From BBC Learning English.', 'intermediate', 0, 55],
            ['uNOnyMRdDTA', '12 Ways to Improve Your English Listening Skills', 'Practical listening strategies: shadowing, varied accents, active vs passive listening and how to use subtitles properly. From English with Lucy.', 'intermediate', 1, 50],
            ['Ljjiw9mC_Cg', 'Learn All 16 Tenses in Under 30 Minutes', 'Every English tense, from present simple to future perfect continuous, with timelines and examples. Take notes and revisit the PDF in the Documents section. From English with Lucy.', 'intermediate', 1, 44],
            ['p_LBYUO8Ai4', 'Learn the Perfect Tenses Easily in 12 Minutes', 'Present, past and future perfect side by side, so you can finally see when to choose each one. From English with Lucy.', 'intermediate', 1, 38],
            ['7NGLHYVmr00', 'Present Perfect vs Present Perfect Continuous', 'All the differences between the two forms, with a quiz at the end to test yourself. From English with Lucy.', 'intermediate', 2, 32],
            ['Emdc5LIhHa4', 'The 50 Most Important Phrasal Verbs in English', 'Fifty high-frequency phrasal verbs with meanings and example sentences. Pair it with the Phrasal Verbs Workbook. From English with Lucy.', 'intermediate', 2, 27],
            ['jXK006PVir4', '15 Phrasal Verbs with GET in Context', 'Get by, get across, get through and more, each shown in a real sentence. From English with Lucy.', 'intermediate', 2, 22],
            ['opKPVqxE_QY', 'English Words You Are Probably Mispronouncing', 'Common words that trip up even advanced learners, and how to say them the way native speakers do. From Rachel\'s English.', 'intermediate', 2, 17],

            // — Advanced —
            ['u0cjcomXtd4', '21 Advanced Phrases (C1) to Build Your Vocabulary', 'Natural, high-level phrases to replace basic vocabulary in speaking and writing. From English with Lucy.', 'advanced', 3, 13],
            ['zudrMkqu12g', 'Better English Conversations: Increase Your Advanced Vocabulary', 'How to bring advanced vocabulary into everyday conversation without sounding unnatural. From English with Lucy.', 'advanced', 3, 10],
            ['76IQ-r2Ob6U', 'If You Know These 17 Advanced Words, You Have C2 Vocabulary', 'A self-check of rare but genuinely useful words used by educated native speakers. From English with Lucy.', 'advanced', 3, 7],
            ['niPHrqdmgrA', 'What Makes American English So Fast?', 'A deep dive into linking, reductions and flapping that explain why native speech is hard to follow. From Rachel\'s English.', 'advanced', 3, 4],
            ['lgifm12Mo7w', 'Fast and Clear Advanced English Practice: Listening and Speaking Podcast', 'An advanced listening exercise at near-native speed, ideal for the final stretch to fluency. From English with Lucy.', 'advanced', 3, 2],
        ];

        foreach ($videos as [$id, $title, $description, $level, $access, $daysAgo]) {
            $video = Video::firstOrNew(['youtube_url' => "https://www.youtube.com/watch?v={$id}"]);
            $video->fill([
                'title' => $title,
                'description' => $description,
                'course_level' => CourseLevel::from($level),
                'required_access_level' => $access,
                'created_by' => $admin->id,
            ]);
            $video->created_at = $video->created_at ?? now()->subDays($daysAgo);
            $video->save();
        }
    }
}
