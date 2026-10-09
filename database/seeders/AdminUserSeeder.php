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
     * Seed the administrator account.
     *
     * Credentials come from config/deploy.php (ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD).
     * Local development keeps the old convenience login (admin@example.com / password); any
     * other environment must set ADMIN_PASSWORD. The password is only ever set when the account
     * is created, so re-running this (every deploy does) never resets one the admin has changed.
     *
     * 'role' and 'status' are deliberately not in User::$fillable (to prevent
     * privilege escalation via any user-facing form), so they're set here via
     * forceFill() rather than mass-assignment.
     */
    public function run(): void
    {
        $config = config('deploy.admin');

        $password = $config['password']
            ?: (app()->environment('local', 'testing') ? 'password' : null);

        if (! $password) {
            $this->command?->warn('AdminUserSeeder skipped: set ADMIN_PASSWORD to create the administrator.');

            return;
        }

        $admin = User::firstOrNew(['email' => $config['email']]);

        if (! $admin->exists) {
            $admin->name = $config['name'];
            $admin->password = Hash::make($password);
            $admin->email_verified_at = now();
        }

        $admin->forceFill([
            'role' => UserRole::Admin,
            'status' => UserStatus::Active,
        ])->save();
    }
}
