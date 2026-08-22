<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed the plans table, matching the pricing copy in resources/views/welcome.blade.php.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'code' => 'free',
                'fee' => 0,
                'access_level' => 0,
                'description' => 'Free access to public lessons and resources.',
            ],
            [
                'name' => 'Basic',
                'code' => 'basic',
                'fee' => 1500,
                'access_level' => 1,
                'description' => 'Perfect for beginners. Access to basic lessons.',
            ],
            [
                'name' => 'Standard',
                'code' => 'standard',
                'fee' => 2000,
                'access_level' => 2,
                'description' => 'Ideal for intermediate learners. Access to all lessons, expert tips and tricks, priority support.',
            ],
            [
                'name' => 'Premium',
                'code' => 'premium',
                'fee' => 3000,
                'access_level' => 3,
                'description' => 'For advanced learners. Access to all lessons, 1-on-1 coaching, exclusive content.',
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
