<?php

use App\Models\Doc;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

// isAccessibleBy() is a real authorization boundary (VideoController@show,
// DocController@download) — these tests guard against a future regression there,
// since nothing else in the suite exercises it at the HTTP layer.

test('a student below the required access level cannot watch a gated video', function () {
    $creator = User::factory()->create();
    $video = Video::create([
        'title' => 'Premium Lesson',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'course_level' => 'intermediate',
        'required_access_level' => 2,
        'created_by' => $creator->id,
    ]);

    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('videos.show', $video))
        ->assertForbidden();
});

test('a student below the required access level cannot download a gated document', function () {
    Storage::fake('local');

    $creator = User::factory()->create();
    $doc = Doc::create([
        'title' => 'Premium Worksheet',
        'file_path' => 'docs/fake.pdf',
        'original_filename' => 'fake.pdf',
        'required_access_level' => 2,
        'created_by' => $creator->id,
    ]);

    $student = User::factory()->create();

    $this->actingAs($student)
        ->get(route('documents.download', $doc))
        ->assertForbidden();
});

test('a student with a matching active subscription can watch a gated video', function () {
    $creator = User::factory()->create();
    $plan = Plan::create([
        'name' => 'Standard', 'code' => 'standard-video-test', 'fee' => 2000, 'access_level' => 2,
    ]);
    $video = Video::create([
        'title' => 'Premium Lesson',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'course_level' => 'intermediate',
        'required_access_level' => 2,
        'created_by' => $creator->id,
    ]);

    $student = User::factory()->create();
    Subscription::create([
        'user_id' => $student->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'amount' => $plan->fee,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDays(29),
    ]);

    $this->actingAs($student)
        ->get(route('videos.show', $video))
        ->assertOk();
});

test('a student with a matching active subscription can download a gated document', function () {
    Storage::fake('local');
    Storage::disk('local')->put('docs/fake.pdf', 'fake pdf content');

    $creator = User::factory()->create();
    $plan = Plan::create([
        'name' => 'Standard', 'code' => 'standard-doc-test', 'fee' => 2000, 'access_level' => 2,
    ]);
    $doc = Doc::create([
        'title' => 'Premium Worksheet',
        'file_path' => 'docs/fake.pdf',
        'original_filename' => 'fake.pdf',
        'required_access_level' => 2,
        'created_by' => $creator->id,
    ]);

    $student = User::factory()->create();
    Subscription::create([
        'user_id' => $student->id,
        'plan_id' => $plan->id,
        'status' => 'active',
        'amount' => $plan->fee,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addDays(29),
    ]);

    $this->actingAs($student)
        ->get(route('documents.download', $doc))
        ->assertOk();
});
