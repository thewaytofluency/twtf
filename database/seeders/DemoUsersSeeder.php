<?php

namespace Database\Seeders;

use App\Enums\SubscriptionMethod;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Realistic student roster covering every state the admin screens have to deal with: free users,
 * active subscribers on each plan, an expired subscription, pending requests (with a payment
 * proof image to review), a rejected request, and a deactivated account. Password for all: "password".
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $plans = Plan::pluck('id', 'code');
        $admin = User::where('role', UserRole::Admin)->firstOrFail();

        // [name, contact, joined N days ago, status, subscription state]
        // Subscription state: null | [plan code, 'active'|'expiring'|'expired'|'pending'|'rejected', channel]
        $students = [
            ['Ana Macuácua', '+258 84 312 4410', 160, 'active', ['premium', 'active', 'M-Pesa']],
            ['Carlos Nhaca', '+258 82 774 0921', 140, 'active', ['standard', 'active', 'e-Mola']],
            ['Beatriz Chissano', '+258 84 905 1187', 120, 'active', ['standard', 'expiring', 'M-Pesa']],
            ['Dércio Mabunda', '+258 86 223 6650', 100, 'active', ['basic', 'active', 'M-Pesa']],
            ['Fátima Sitoe', '+258 87 418 3092', 90, 'active', ['basic', 'expired', 'Bank transfer']],
            ['Gilberto Tembe', '+258 84 660 2275', 75, 'active', ['premium', 'active', 'Bank transfer']],
            ['Helena Cumbe', '+258 82 190 8843', 60, 'active', ['standard', 'pending', 'M-Pesa']],
            ['Inácio Bila', '+258 85 507 7316', 45, 'active', ['basic', 'pending', 'e-Mola']],
            ['Joana Mondlane', '+258 84 833 1409', 40, 'active', ['premium', 'rejected', 'M-Pesa']],
            ['Kelvin Muianga', '+258 86 951 2208', 30, 'active', null],
            ['Lúcia Machava', '+258 82 346 5571', 21, 'active', null],
            ['Manuel Zandamela', '+258 87 072 6694', 14, 'active', ['basic', 'active', 'e-Mola']],
            ['Nádia Guambe', '+258 84 481 9930', 8, 'active', null],
            ['Osvaldo Langa', '+258 85 229 4467', 150, 'inactive', ['standard', 'expired', 'M-Pesa']],
        ];

        foreach ($students as [$name, $contact, $joinedDaysAgo, $status, $subscription]) {
            $joinedAt = now()->subDays($joinedDaysAgo)->setTime(random_int(7, 21), random_int(0, 59));

            $user = User::firstOrNew(['email' => $this->email($name)]);
            $user->fill(['name' => $name, 'contact' => $contact, 'password' => Hash::make(config('deploy.demo_password'))]);
            $user->email_verified_at = $joinedAt;
            $user->created_at = $user->created_at ?? $joinedAt;
            $user->forceFill([
                'role' => UserRole::Student,
                'status' => $status === 'inactive' ? UserStatus::Inactive : UserStatus::Active,
            ])->save();

            if ($subscription && ! $user->subscriptions()->exists()) {
                $this->subscribe($user, $plans[$subscription[0]], $subscription[1], $subscription[2], $admin, $joinedAt);
            }
        }
    }

    private function subscribe(User $user, int $planId, string $state, string $channel, User $admin, $joinedAt): void
    {
        $plan = Plan::find($planId);
        $requestedAt = $joinedAt->copy()->addDays(1);

        $data = [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'method' => SubscriptionMethod::Manual,
            'payment_channel' => $channel,
            'amount' => $plan->fee,
            'proof_path' => $this->proofImage($user, $plan, $channel),
        ];

        $subscription = new Subscription($data);
        $subscription->created_at = $requestedAt;

        match ($state) {
            'pending' => $subscription->status = SubscriptionStatus::Pending,
            'rejected' => $subscription->fill([
                'status' => SubscriptionStatus::Rejected,
                'reviewed_by' => $admin->id,
                'reviewed_at' => $requestedAt->copy()->addHours(5),
                'admin_notes' => 'The amount on the receipt does not match the plan fee. Please resubmit with the correct proof.',
            ]),
            default => $this->approved($subscription, $state, $admin, $requestedAt),
        };

        $subscription->save();
    }

    private function approved(Subscription $subscription, string $state, User $admin, $requestedAt): void
    {
        // active: started ~12 days ago; expiring: ends in 3 days; expired: ended 10 days ago.
        $startsAt = match ($state) {
            'expiring' => now()->subDays(27),
            'expired' => now()->subDays(40),
            default => now()->subDays(12),
        };

        $subscription->fill([
            'status' => SubscriptionStatus::Active,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addDays(30),
            'reviewed_by' => $admin->id,
            'reviewed_at' => $startsAt,
        ]);
        $subscription->created_at = $startsAt->copy()->subHours(6);
    }

    /** A simple generated "payment receipt" so the admin proof viewer has a real image to show. */
    private function proofImage(User $user, Plan $plan, string $channel): string
    {
        $path = 'proofs/demo-'.md5($user->email).'.png';

        if (Storage::disk('local')->exists($path)) {
            return $path;
        }

        $image = imagecreatetruecolor(480, 300);
        $white = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 31, 41, 55);
        $grey = imagecolorallocate($image, 107, 114, 128);
        $green = imagecolorallocate($image, 22, 163, 74);
        imagefill($image, 0, 0, $white);
        imagefilledrectangle($image, 0, 0, 480, 46, $green);

        imagestring($image, 5, 16, 15, strtoupper($channel).' - PAYMENT RECEIPT', $white);
        imagestring($image, 4, 16, 76, 'Amount:    '.number_format((float) $plan->fee, 2).' MZN', $dark);
        imagestring($image, 4, 16, 108, 'Reference: TX'.strtoupper(substr(md5($user->email), 0, 10)), $dark);
        imagestring($image, 4, 16, 140, 'To:        The Way to Fluency', $dark);
        imagestring($image, 4, 16, 172, 'Status:    COMPLETED', $dark);
        imagestring($image, 3, 16, 250, 'Demo data - not a real transaction', $grey);

        ob_start();
        imagepng($image);
        $png = ob_get_clean();

        Storage::disk('local')->put($path, $png);

        return $path;
    }

    private function email(string $name): string
    {
        return Str::slug($name, '.').'@example.com';
    }
}
