<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed a development admin account. Rotate/replace credentials before production.
     *
     * 'role' and 'status' are deliberately not in User::$fillable (to prevent
     * privilege escalation via any user-facing form), so they're set here via
     * forceFill() rather than mass-assignment.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->forceFill([
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
        ])->save();
    }
}
