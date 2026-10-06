<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Realistic demo data for exercising the whole platform: students in every subscription state,
 * real YouTube lessons, downloadable PDF study documents, blog articles, comments and likes.
 *
 *   php artisan db:seed --class=DemoContentSeeder
 *
 * Safe to re-run (everything is keyed on a natural identifier). Runs automatically from
 * DatabaseSeeder in the local environment only. Needs plans + admin, so it seeds them first.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            AdminUserSeeder::class,
            DemoUsersSeeder::class,
            DemoVideosSeeder::class,
            DemoDocsSeeder::class,
            DemoBlogSeeder::class,
            DemoEngagementSeeder::class,
        ]);
    }
}
