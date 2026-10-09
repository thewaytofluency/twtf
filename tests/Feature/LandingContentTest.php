<?php

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\ImpactItem;
use App\Models\User;
use App\Support\CardGrid;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function landingAdmin(): User
{
    $admin = User::factory()->create();
    $admin->forceFill(['role' => UserRole::Admin])->save();

    return $admin;
}

function coursePayload(array $overrides = []): array
{
    return [
        'title' => 'Business English',
        'description' => 'English for work.',
        'badge_type' => 'emoji',
        'emoji' => '💼',
        'accent' => 'amber',
        'is_active' => '1',
        ...$overrides,
    ];
}

test('students cannot manage landing content', function () {
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('admin.courses.index'))->assertForbidden();
    $this->actingAs($student)->get(route('admin.impact.index'))->assertForbidden();
    $this->actingAs($student)->post(route('admin.courses.store'), coursePayload())->assertForbidden();
});

test('admin pages render', function () {
    $admin = landingAdmin();
    $course = Course::create(['title' => 'C', 'description' => 'd']);
    $item = ImpactItem::create(['title' => 'I', 'description' => 'd']);

    foreach ([
        route('admin.courses.index'), route('admin.courses.create'), route('admin.courses.edit', $course),
        route('admin.impact.index'), route('admin.impact.create'), route('admin.impact.edit', $item),
    ] as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }
});

test('an admin can create, update and delete a course', function () {
    $admin = landingAdmin();

    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['cta_url' => 'https://example.com/biz']))
        ->assertRedirect(route('admin.courses.index'));

    $course = Course::firstOrFail();
    expect($course->title)->toBe('Business English');
    expect($course->cta_url)->toBe('https://example.com/biz');
    expect($course->sort_order)->toBe(1);
    expect($course->is_active)->toBeTrue();

    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['title' => 'Renamed', 'is_active' => '0', 'sort_order' => 9]))
        ->assertRedirect();
    expect($course->fresh()->title)->toBe('Renamed');
    expect($course->fresh()->is_active)->toBeFalse();
    expect($course->fresh()->sort_order)->toBe(9);

    $this->actingAs($admin)->delete(route('admin.courses.destroy', $course))->assertRedirect();
    expect(Course::count())->toBe(0);
});

test('course validation rejects unknown accents and unsafe links', function () {
    $admin = landingAdmin();

    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['accent' => 'neon']))->assertSessionHasErrors('accent');
    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['cta_url' => 'javascript:alert(1)']))->assertSessionHasErrors('cta_url');
    expect(Course::count())->toBe(0);
});

test('an admin can create, update and delete an impact item', function () {
    $admin = landingAdmin();

    $this->actingAs($admin)->post(route('admin.impact.store'), [
        'title' => 'Learners', 'description' => 'Taught', 'badge_type' => 'emoji', 'stat' => '2,500+', 'emoji' => '🎓', 'is_active' => '1',
    ])->assertRedirect(route('admin.impact.index'));

    $item = ImpactItem::firstOrFail();
    expect($item->stat)->toBe('2,500+');

    $this->actingAs($admin)->put(route('admin.impact.update', $item), ['title' => 'Learners!', 'description' => 'Taught', 'badge_type' => 'emoji', 'is_active' => '1'])->assertRedirect();
    expect($item->fresh()->title)->toBe('Learners!');
    expect($item->fresh()->stat)->toBeNull();

    $this->actingAs($admin)->delete(route('admin.impact.destroy', $item))->assertRedirect();
    expect(ImpactItem::count())->toBe(0);
});

test('images can be uploaded, replaced and removed and are deleted with the item', function () {
    Storage::fake('public');
    $admin = landingAdmin();

    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['badge_type' => 'image', 'image' => UploadedFile::fake()->image('a.jpg')]));
    $course = Course::firstOrFail();
    $first = $course->image;
    Storage::disk('public')->assertExists($first);

    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['badge_type' => 'image', 'image' => UploadedFile::fake()->image('b.jpg')]));
    Storage::disk('public')->assertMissing($first);
    $second = $course->fresh()->image;
    Storage::disk('public')->assertExists($second);

    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['remove_image' => '1']));
    expect($course->fresh()->image)->toBeNull();
    Storage::disk('public')->assertMissing($second);

    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['badge_type' => 'image', 'image' => UploadedFile::fake()->image('c.jpg')]));
    $third = $course->fresh()->image;
    $this->actingAs($admin)->delete(route('admin.courses.destroy', $course));
    Storage::disk('public')->assertMissing($third);
});

test('items move up and down, even when they share a sort order', function () {
    $admin = landingAdmin();
    $a = Course::create(['title' => 'A', 'description' => 'd']);
    $b = Course::create(['title' => 'B', 'description' => 'd']);
    $c = Course::create(['title' => 'C', 'description' => 'd']);

    $order = fn () => Course::ordered()->pluck('title')->all();
    expect($order())->toBe(['A', 'B', 'C']);

    $this->actingAs($admin)->patch(route('admin.courses.move', $c), ['direction' => 'up']);
    expect($order())->toBe(['A', 'C', 'B']);

    $this->actingAs($admin)->patch(route('admin.courses.move', $a), ['direction' => 'down']);
    expect($order())->toBe(['C', 'A', 'B']);

    // At the edges nothing happens (and nothing breaks).
    $this->actingAs($admin)->patch(route('admin.courses.move', $c), ['direction' => 'up']);
    expect($order())->toBe(['C', 'A', 'B']);

    $this->actingAs($admin)->patch(route('admin.courses.move', $c), ['direction' => 'sideways'])->assertSessionHasErrors('direction');
});

test('the landing page shows only visible courses and impact items, in order', function () {
    Course::create(['title' => 'Second Course', 'description' => 'd', 'sort_order' => 2]);
    Course::create(['title' => 'First Course', 'description' => 'd', 'sort_order' => 1, 'emoji' => '🌱']);
    Course::create(['title' => 'Hidden Course', 'description' => 'd', 'is_active' => false]);
    ImpactItem::create(['title' => 'Big Number', 'description' => 'd', 'stat' => '9,999+']);
    ImpactItem::create(['title' => 'Hidden Impact', 'description' => 'd', 'is_active' => false]);

    $this->get('/')
        ->assertOk()
        ->assertSeeInOrder(['First Course', 'Second Course'])
        ->assertDontSee('Hidden Course')
        ->assertSee('Big Number')
        ->assertSee('9,999+')
        ->assertDontSee('Hidden Impact');
});

test('a course button goes to its link, or to sign-up by default', function () {
    Course::create(['title' => 'Custom Link', 'description' => 'd', 'cta_url' => 'https://example.com/go']);
    Course::create(['title' => 'Default Link', 'description' => 'd']);

    $this->get('/')->assertSee('https://example.com/go', false)->assertSee(route('register'), false);
});

test('the landing page copes with no courses or impact items', function () {
    $this->get('/')->assertOk()->assertSee('Courses are coming soon.')->assertSee('check back soon');
});

test('card widths adapt to the number of cards', function () {
    expect(CardGrid::cardWidth(4))->toContain('xl:w-[calc(25%-1.5rem)]');

    foreach ([1, 2, 3, 5, 7] as $count) {
        expect(CardGrid::cardWidth($count))->toBe('w-full md:w-[22rem]');
    }
});

test('choosing the image badge requires an image', function () {
    Storage::fake('public');
    $admin = landingAdmin();

    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['badge_type' => 'image']))->assertSessionHasErrors('image');
    $this->actingAs($admin)->post(route('admin.impact.store'), ['title' => 'T', 'description' => 'd', 'badge_type' => 'image'])->assertSessionHasErrors('image');
    expect(Course::count())->toBe(0)->and(ImpactItem::count())->toBe(0);

    // An existing image satisfies it, unless it is being removed in the same request.
    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['badge_type' => 'image', 'image' => UploadedFile::fake()->image('a.jpg')]));
    $course = Course::firstOrFail();
    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['badge_type' => 'image']))->assertSessionDoesntHaveErrors();
    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['badge_type' => 'image', 'remove_image' => '1']))->assertSessionHasErrors('image');
});

test('an invalid badge type is rejected', function () {
    $this->actingAs(landingAdmin())->post(route('admin.courses.store'), coursePayload(['badge_type' => 'video']))->assertSessionHasErrors('badge_type');
});

test('the landing page shows the chosen badge only', function () {
    Course::create(['title' => 'Picture Course', 'description' => 'd', 'badge_type' => 'image', 'image' => 'landing/pic.jpg', 'emoji' => '🅿️']);
    Course::create(['title' => 'Emoji Course', 'description' => 'd', 'badge_type' => 'emoji', 'image' => 'landing/unused.jpg', 'emoji' => '🔥']);
    ImpactItem::create(['title' => 'Picture Impact', 'description' => 'd', 'badge_type' => 'image', 'image' => 'landing/impact.jpg']);

    $this->get('/')
        ->assertSee('/storage/landing/pic.jpg', false)
        ->assertSee('/storage/landing/impact.jpg', false)
        ->assertDontSee('/storage/landing/unused.jpg', false)
        ->assertSee('🔥')
        ->assertDontSee('🅿️');
});

test('switching back to the emoji badge keeps the uploaded image for later', function () {
    Storage::fake('public');
    $admin = landingAdmin();

    $this->actingAs($admin)->post(route('admin.courses.store'), coursePayload(['badge_type' => 'image', 'image' => UploadedFile::fake()->image('a.jpg')]));
    $course = Course::firstOrFail();

    $this->actingAs($admin)->put(route('admin.courses.update', $course), coursePayload(['badge_type' => 'emoji']));

    expect($course->fresh()->badge_type)->toBe('emoji')->and($course->fresh()->image)->not->toBeNull();
    Storage::disk('public')->assertExists($course->fresh()->image);
});
