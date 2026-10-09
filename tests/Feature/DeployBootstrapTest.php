<?php

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\ImpactItem;
use App\Models\Plan;
use App\Models\SocialMediaLink;
use App\Models\User;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config([
        'deploy.token' => null,
        'deploy.demo' => false,
        'deploy.auto' => false,
        'deploy.min_interval' => 0,
        'deploy.admin.password' => 'S3cret-pass',
        'deploy.admin.email' => 'boss@example.com',
    ]);
});

test('app:deploy sets up a fresh database: essentials, admin and storage', function () {
    $this->artisan('app:deploy')->assertSuccessful();

    expect(Plan::count())->toBe(4)
        ->and(SocialMediaLink::count())->toBe(5)
        ->and(Course::count())->toBe(3)
        ->and(ImpactItem::count())->toBe(3);

    $admin = User::where('email', 'boss@example.com')->first();
    expect($admin)->not->toBeNull()
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and(password_verify('S3cret-pass', $admin->password))->toBeTrue();
});

test('running the bootstrap again changes nothing and never overwrites edits', function () {
    $this->artisan('app:deploy')->assertSuccessful();

    // The admin changes their password and deletes a course; a redeploy must respect both.
    $admin = User::where('email', 'boss@example.com')->first();
    $admin->update(['password' => 'changed-by-admin']);
    $oldHash = $admin->fresh()->password;
    Plan::where('code', 'basic')->update(['fee' => 999]);

    $this->artisan('app:deploy')->assertSuccessful();

    expect(User::where('role', 'admin')->count())->toBe(1)
        ->and(User::where('email', 'boss@example.com')->first()->password)->toBe($oldHash)
        ->and(Plan::count())->toBe(4)
        ->and((float) Plan::where('code', 'basic')->first()->fee)->toBe(999.0)
        ->and(Course::count())->toBe(3);
});

test('outside local development no admin is created without an ADMIN_PASSWORD', function () {
    config(['deploy.admin.password' => null]);
    $this->app['env'] = 'production';

    $this->artisan('app:deploy')->assertSuccessful();

    expect(User::where('role', 'admin')->exists())->toBeFalse()
        ->and(Plan::count())->toBe(4);
});

test('the demo content loads once and is then left alone', function () {
    Storage::fake('local');
    Storage::fake('public');

    $this->artisan('app:deploy', ['--demo' => true])->assertSuccessful();
    $videos = Video::count();
    expect($videos)->toBeGreaterThan(10)->and(User::where('role', 'student')->count())->toBeGreaterThan(5);

    // The admin deletes one demo video; running the demo bootstrap again must not bring it back.
    Video::first()->delete();
    $this->artisan('app:deploy', ['--demo' => true])->assertSuccessful();

    expect(Video::count())->toBe($videos - 1);
});

test('demo students use the configured password', function () {
    Storage::fake('local');
    Storage::fake('public');
    config(['deploy.demo_password' => 'demo-pass-123']);

    $this->artisan('app:deploy', ['--demo' => true])->assertSuccessful();

    $student = User::where('email', 'ana.macuacua@example.com')->first();
    expect(password_verify('demo-pass-123', $student->password))->toBeTrue()
        ->and(password_verify('password', $student->password))->toBeFalse();
});

test('the deploy endpoint runs the bootstrap, without a token when none is configured', function () {
    $this->postJson('/__deploy')
        ->assertOk()
        ->assertJson(['ok' => true])
        ->assertJsonStructure(['steps']);

    expect(Plan::count())->toBe(4);
});

test('the deploy endpoint only accepts POST', function () {
    $this->get('/__deploy')->assertStatus(405);
});

test('with a deploy token configured the endpoint rejects missing and wrong tokens', function () {
    config(['deploy.token' => 'right-token']);

    $this->postJson('/__deploy')->assertForbidden();
    $this->postJson('/__deploy', [], ['Authorization' => 'Bearer wrong-token'])->assertForbidden();
    expect(Plan::count())->toBe(0);

    $this->postJson('/__deploy', [], ['Authorization' => 'Bearer right-token'])->assertOk();
    expect(Plan::count())->toBe(4);

    Plan::query()->delete();
    $this->postJson('/__deploy', [], ['X-Deploy-Token' => 'right-token'])->assertOk();
    expect(Plan::count())->toBe(4);
});

test('the endpoint refuses to run twice in quick succession', function () {
    config(['deploy.min_interval' => 60]);
    @unlink(sys_get_temp_dir().DIRECTORY_SEPARATOR.'twtf-deploy-endpoint');

    $this->postJson('/__deploy')->assertOk();
    $this->postJson('/__deploy')->assertStatus(429)->assertHeader('Retry-After');

    @unlink(sys_get_temp_dir().DIRECTORY_SEPARATOR.'twtf-deploy-endpoint');
});

test('with AUTO_DEPLOY on, the first request after a release sets the site up', function () {
    config(['deploy.auto' => true, 'deploy.release' => 'test-'.uniqid()]);

    expect(Plan::count())->toBe(0);

    $this->get('/')->assertOk();

    expect(Plan::count())->toBe(4)->and(Course::count())->toBe(3);
});

test('with AUTO_DEPLOY off, requests do not touch the database setup', function () {
    $this->get('/')->assertOk();

    expect(Plan::count())->toBe(0);
});

test('uploaded media is served from the public disk without a storage symlink', function () {
    Storage::fake('public');
    Storage::disk('public')->put('blog/covers/photo.jpg', 'fake-image-bytes');

    $response = $this->get('/storage/blog/covers/photo.jpg');

    $response->assertOk();
    expect($response->streamedContent())->toBe('fake-image-bytes');
    expect($response->headers->get('Cache-Control'))->toContain('max-age=86400');
});

test('missing media is a 404 and paths cannot climb out of the public disk', function () {
    Storage::fake('public');
    Storage::disk('local')->put('secret.txt', 'private');

    $this->get('/storage/nope.jpg')->assertNotFound();
    $this->get('/storage/../private/secret.txt')->assertNotFound();
    $this->get('/storage/blog/../../private/secret.txt')->assertNotFound();
    $this->get('/storage/%2e%2e/private/secret.txt')->assertNotFound();
});

test('the private disk is never reachable through /storage', function () {
    Storage::fake('public');
    Storage::fake('local');
    Storage::disk('local')->put('docs/secret.pdf', 'private');

    $this->get('/storage/docs/secret.pdf')->assertNotFound();
});
