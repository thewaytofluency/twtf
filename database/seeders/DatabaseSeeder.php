<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            AdminUserSeeder::class,
        ]);

        // User::factory(10)->create();

        // 'role' defaults to student at the DB level; set explicitly here for clarity.
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->forceFill(['role' => UserRole::Student])->save();
    }
}
