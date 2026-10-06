<?php

use App\Enums\UserRole;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

function planAdmin(): User
{
    $admin = User::factory()->create();
    $admin->forceFill(['role' => UserRole::Admin])->save();

    return $admin;
}

function planPayload(array $overrides = []): array
{
    return [
        'name' => 'Gold',
        'code' => 'gold',
        'fee' => 2500,
        'access_level' => 3,
        'description' => 'Top tier.',
        'features' => "Everything\n\n  Coaching  \n",
        'is_popular' => '1',
        'is_active' => '1',
        ...$overrides,
    ];
}

test('students cannot reach plan management', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.plans.index'))
        ->assertForbidden();
});

test('an admin can create a plan, with features parsed one per line', function () {
    $admin = planAdmin();

    $this->actingAs($admin)->post(route('admin.plans.store'), planPayload())
        ->assertRedirect(route('admin.plans.index'));

    $plan = Plan::where('code', 'gold')->firstOrFail();
    expect($plan->features)->toBe(['Everything', 'Coaching']);
    expect($plan->is_popular)->toBeTrue();
});

test('marking a plan popular clears the badge from the previous one', function () {
    $admin = planAdmin();
    $old = Plan::create(['name' => 'Old', 'code' => 'old', 'fee' => 10, 'access_level' => 1, 'is_popular' => true]);

    $this->actingAs($admin)->post(route('admin.plans.store'), planPayload());

    expect($old->fresh()->is_popular)->toBeFalse();
    expect(Plan::where('is_popular', true)->count())->toBe(1);
});

test('an admin can update a plan without tripping the unique code rule on itself', function () {
    $admin = planAdmin();
    $plan = Plan::create(['name' => 'Gold', 'code' => 'gold', 'fee' => 100, 'access_level' => 1]);

    $this->actingAs($admin)
        ->put(route('admin.plans.update', $plan), planPayload(['fee' => 999, 'is_popular' => '0']))
        ->assertRedirect(route('admin.plans.index'));

    expect((float) $plan->fresh()->fee)->toBe(999.0);
});

test('a plan with subscriptions cannot be deleted, an unused one can', function () {
    $admin = planAdmin();
    $used = Plan::create(['name' => 'Used', 'code' => 'used', 'fee' => 100, 'access_level' => 1]);
    $unused = Plan::create(['name' => 'Unused', 'code' => 'unused', 'fee' => 100, 'access_level' => 1]);
    Subscription::create([
        'user_id' => User::factory()->create()->id,
        'plan_id' => $used->id,
        'method' => 'manual',
        'amount' => 100,
    ]);

    $this->actingAs($admin)->delete(route('admin.plans.destroy', $used))->assertSessionHasErrors('plan');
    expect(Plan::find($used->id))->not->toBeNull();

    $this->actingAs($admin)->delete(route('admin.plans.destroy', $unused))->assertRedirect(route('admin.plans.index'));
    expect(Plan::find($unused->id))->toBeNull();
});

test('the landing page lists active paid plans for any number of plans', function (int $count) {
    foreach (range(1, $count) as $i) {
        Plan::create(['name' => "Plan {$i}", 'code' => "plan-{$i}", 'fee' => 100 * $i, 'access_level' => $i, 'features' => ["Feature {$i}"]]);
    }
    Plan::create(['name' => 'Hidden', 'code' => 'hidden', 'fee' => 50, 'access_level' => 1, 'is_active' => false]);
    Plan::create(['name' => 'FreeTier', 'code' => 'free-tier', 'fee' => 0, 'access_level' => 0]);

    $response = $this->get('/')->assertOk()->assertDontSee('Hidden')->assertDontSee('FreeTier');

    foreach (range(1, $count) as $i) {
        $response->assertSee("Plan {$i}")->assertSee("Feature {$i}");
    }
})->with([1, 2, 3, 4, 5, 7]);

test('the landing page copes with no plans', function () {
    $this->get('/')->assertOk()->assertSee('Plans are coming soon.');
});

test('fees format without noise', function () {
    expect((new Plan(['fee' => 1500]))->formattedFee())->toBe('1500 MZN');
    expect((new Plan(['fee' => 1500.5]))->formattedFee())->toBe('1500.50 MZN');
    expect((new Plan(['fee' => 0]))->formattedFee())->toBe('Free');
});
