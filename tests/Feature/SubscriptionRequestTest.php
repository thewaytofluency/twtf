<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('a student can submit a subscription request, which stays pending (not auto-approved)', function () {
    Storage::fake('local');

    $plan = Plan::create(['name' => 'Standard', 'code' => 'standard-req-test', 'fee' => 2000, 'access_level' => 2]);
    $student = User::factory()->create();

    $response = $this->actingAs($student)->post(route('subscription.store', $plan), [
        'proof' => UploadedFile::fake()->image('proof.jpg'),
        'payment_channel' => 'M-Pesa',
    ]);

    $response->assertRedirect(route('subscription.index'));

    $subscription = Subscription::where('user_id', $student->id)->first();
    expect($subscription)->not->toBeNull();
    expect($subscription->status->value)->toBe('pending');
    expect($subscription->plan_id)->toBe($plan->id);
    expect($student->fresh()->currentAccessLevel())->toBe(0);
    Storage::disk('local')->assertExists($subscription->proof_path);
});

test('a student with a pending request cannot submit a second one', function () {
    Storage::fake('local');

    $plan = Plan::create(['name' => 'Standard', 'code' => 'standard-dupe-test', 'fee' => 2000, 'access_level' => 2]);
    $student = User::factory()->create();

    Subscription::create([
        'user_id' => $student->id,
        'plan_id' => $plan->id,
        'method' => 'manual',
        'amount' => $plan->fee,
    ]);

    $response = $this->actingAs($student)->get(route('subscription.request', $plan));
    $response->assertRedirect(route('subscription.index'));

    $response = $this->actingAs($student)->post(route('subscription.store', $plan), [
        'proof' => UploadedFile::fake()->image('proof.jpg'),
    ]);
    $response->assertRedirect(route('subscription.index'));

    expect(Subscription::where('user_id', $student->id)->count())->toBe(1);
});

test('a student cannot request the free plan', function () {
    $freePlan = Plan::create(['name' => 'Free', 'code' => 'free-req-test', 'fee' => 0, 'access_level' => 0]);
    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('subscription.request', $freePlan))
        ->assertNotFound();
});
