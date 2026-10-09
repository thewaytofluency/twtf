<?php

use App\Models\BlogPost;
use App\Models\Doc;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function flowVideo(string $title, string $level, int $order, int $access = 0): Video
{
    return Video::create([
        'title' => $title,
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'course_level' => $level,
        'required_access_level' => $access,
        'sort_order' => $order,
        'created_by' => User::factory()->create()->id,
    ]);
}

function flowDoc(string $title, ?string $level, int $order, int $access = 0, string $file = 'x.pdf'): Doc
{
    return Doc::create([
        'title' => $title,
        'file_path' => "docs/{$file}",
        'original_filename' => $file,
        'file_size' => 2048,
        'course_level' => $level,
        'required_access_level' => $access,
        'sort_order' => $order,
        'created_by' => User::factory()->create()->id,
    ]);
}

function subscribedStudent(int $level): User
{
    $student = User::factory()->create();
    $plan = Plan::create(['name' => "L{$level}", 'code' => "flow-{$level}-".uniqid(), 'fee' => 100, 'access_level' => $level]);
    Subscription::create([
        'user_id' => $student->id, 'plan_id' => $plan->id, 'status' => 'active', 'amount' => 100,
        'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(29),
    ]);

    return $student;
}

test('lessons follow one path: level first, then the admin order', function () {
    $adv = flowVideo('Adv 1', 'advanced', 1);
    $int2 = flowVideo('Int 2', 'intermediate', 2);
    $int1 = flowVideo('Int 1', 'intermediate', 1);
    $beg = flowVideo('Beg 1', 'beginner', 5);

    expect(Video::inSequence()->pluck('title')->all())->toBe(['Beg 1', 'Int 1', 'Int 2', 'Adv 1']);

    // The last lesson of a level leads straight into the next level.
    expect($beg->neighbours()['next']->title)->toBe('Int 1');
    expect($int2->neighbours()['next']->title)->toBe('Adv 1');
    expect($int1->neighbours()['previous']->title)->toBe('Beg 1');
    expect($beg->neighbours()['previous'])->toBeNull();
    expect($adv->neighbours()['next'])->toBeNull();
});

test('the video page links to the previous and next lesson and lists the level playlist', function () {
    $a = flowVideo('Alpha Lesson', 'beginner', 1);
    $b = flowVideo('Bravo Lesson', 'beginner', 2);
    $c = flowVideo('Charlie Lesson', 'beginner', 3);
    flowVideo('Delta Intermediate', 'intermediate', 1);
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('videos.show', $b))
        ->assertOk()
        ->assertSee(route('videos.show', $a), false)
        ->assertSee(route('videos.show', $c), false)
        ->assertSee('Lesson 2 of 3')
        ->assertDontSee('Delta Intermediate');
});

test('crossing a level boundary is called out on the next-lesson card', function () {
    $last = flowVideo('Last Beginner', 'beginner', 1);
    flowVideo('First Intermediate', 'intermediate', 1);

    $this->actingAs(User::factory()->create())->get(route('videos.show', $last))
        ->assertSee('Next level')
        ->assertSee('First Intermediate');
});

test('a locked next lesson is shown with an upgrade link instead of a lesson link', function () {
    $open = flowVideo('Open One', 'beginner', 1);
    $locked = flowVideo('Locked Two', 'beginner', 2, 2);

    $response = $this->actingAs(User::factory()->create())->get(route('videos.show', $open));

    $response->assertOk()
        ->assertSee('Locked Two')
        ->assertSee('Upgrade to unlock')
        ->assertDontSee(route('videos.show', $locked), false);
});

test('opening a video records progress, and the list resumes where the student left off', function () {
    $a = flowVideo('Alpha Lesson', 'beginner', 1);
    $b = flowVideo('Bravo Lesson', 'beginner', 2);
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('videos.index'))->assertSee('Start learning')->assertSee('Alpha Lesson');

    $this->actingAs($student)->get(route('videos.show', $a));
    expect($student->progressFor('video')->get($a->id)->viewed_at)->not->toBeNull();

    $this->actingAs($student)->post(route('progress.update'), ['type' => 'video', 'id' => $a->id, 'completed' => 1]);
    $this->actingAs($student)->get(route('videos.show', $b));

    $this->actingAs($student)->get(route('videos.index'))->assertSee('Continue watching')->assertSee('Bravo Lesson');
});

test('marking complete works as JSON and can be undone', function () {
    $video = flowVideo('Alpha', 'beginner', 1);
    $student = User::factory()->create();

    $this->actingAs($student)->postJson(route('progress.update'), ['type' => 'video', 'id' => $video->id, 'completed' => true])
        ->assertOk()->assertJson(['completed' => true]);
    expect($video->isCompletedBy($student))->toBeTrue();

    $this->actingAs($student)->postJson(route('progress.update'), ['type' => 'video', 'id' => $video->id, 'completed' => false])
        ->assertOk();
    expect($video->isCompletedBy($student))->toBeFalse();
});

test('complete and continue goes to the next lesson, or back when it is locked', function () {
    $a = flowVideo('A', 'beginner', 1);
    $b = flowVideo('B', 'beginner', 2);
    $c = flowVideo('C', 'beginner', 3, 2);
    $student = User::factory()->create();

    $this->actingAs($student)->post(route('progress.update'), ['type' => 'video', 'id' => $a->id, 'completed' => 1, 'continue' => 1])
        ->assertRedirect(route('videos.show', $b));

    $this->actingAs($student)->from(route('videos.show', $b))
        ->post(route('progress.update'), ['type' => 'video', 'id' => $b->id, 'completed' => 1, 'continue' => 1])
        ->assertRedirect(route('videos.show', $b));
});

test('progress cannot be recorded on locked content or with bad input', function () {
    $locked = flowVideo('Locked', 'beginner', 1, 3);
    $student = User::factory()->create();

    $this->actingAs($student)->postJson(route('progress.update'), ['type' => 'video', 'id' => $locked->id, 'completed' => true])->assertForbidden();
    $this->actingAs($student)->postJson(route('progress.update'), ['type' => 'user', 'id' => 1, 'completed' => true])->assertUnprocessable();
    $this->actingAs($student)->postJson(route('progress.update'), ['type' => 'video', 'id' => 99999, 'completed' => true])->assertNotFound();
});

test('guests cannot record progress', function () {
    $video = flowVideo('Any', 'beginner', 1);

    $this->post(route('progress.update'), ['type' => 'video', 'id' => $video->id, 'completed' => 1])->assertRedirect(route('login'));
});

test("one student's progress never shows up for another", function () {
    $video = flowVideo('Shared', 'beginner', 1);
    $alice = User::factory()->create();
    $bob = User::factory()->create();

    $this->actingAs($alice)->postJson(route('progress.update'), ['type' => 'video', 'id' => $video->id, 'completed' => true]);

    expect($video->isCompletedBy($alice))->toBeTrue()->and($video->isCompletedBy($bob))->toBeFalse();
});

test('the document reader shows the preview, neighbours and playlist', function () {
    Storage::fake('local');
    Storage::disk('local')->put('docs/one.pdf', '%PDF-1.4 one');
    $one = flowDoc('Doc One', 'beginner', 1, 0, 'one.pdf');
    $two = flowDoc('Doc Two', 'beginner', 2, 0, 'two.pdf');
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('documents.show', $one))
        ->assertOk()
        ->assertSee(route('documents.preview', $one), false)
        ->assertSee(route('documents.show', $two), false)
        ->assertSee('Doc Two');

    $this->actingAs($student)->get(route('documents.preview', $one))->assertOk();
});

test('non-previewable documents are download-only', function () {
    $zip = flowDoc('Archive', 'beginner', 1, 0, 'bundle.zip');
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('documents.show', $zip))->assertOk()->assertSee("can't be previewed", false);
    $this->actingAs($student)->get(route('documents.preview', $zip))->assertNotFound();
});

test('locked documents cannot be opened, previewed or have progress recorded', function () {
    $locked = flowDoc('Locked Doc', 'beginner', 1, 2);
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('documents.show', $locked))->assertForbidden();
    $this->actingAs($student)->get(route('documents.preview', $locked))->assertForbidden();
    $this->actingAs($student)->postJson(route('progress.update'), ['type' => 'doc', 'id' => $locked->id, 'completed' => true])->assertForbidden();
});

test('downloading a document counts as studied', function () {
    Storage::fake('local');
    Storage::disk('local')->put('docs/one.pdf', 'pdf');
    $doc = flowDoc('Doc One', 'beginner', 1, 0, 'one.pdf');
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('documents.download', $doc))->assertOk();

    expect($doc->isCompletedBy($student))->toBeTrue();
});

test('a subscribed student can open a previously locked lesson and move on from it', function () {
    $a = flowVideo('Gated A', 'intermediate', 1, 2);
    $b = flowVideo('Gated B', 'intermediate', 2, 2);
    $student = subscribedStudent(2);

    $this->actingAs($student)->get(route('videos.show', $a))->assertOk()->assertSee(route('videos.show', $b), false);
});

test('the blog links to older and newer posts and tracks what was read', function () {
    $author = User::factory()->create();
    $old = BlogPost::create(['title' => 'Old Post', 'slug' => 'old', 'content' => '<p>a</p>', 'author_id' => $author->id, 'published_at' => now()->subDays(3)]);
    $mid = BlogPost::create(['title' => 'Mid Post', 'slug' => 'mid', 'content' => '<p>b</p>', 'author_id' => $author->id, 'published_at' => now()->subDays(2)]);
    $new = BlogPost::create(['title' => 'New Post', 'slug' => 'new', 'content' => '<p>c</p>', 'author_id' => $author->id, 'published_at' => now()->subDay()]);
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('blog.show', $mid))
        ->assertOk()
        ->assertSee(route('blog.show', $old), false)
        ->assertSee(route('blog.show', $new), false)
        ->assertSee('More from the blog');

    expect($student->progressFor('blog_post')->keys()->all())->toBe([$mid->id]);
    $this->actingAs($student)->get(route('blog.index'))->assertSee('Read');
});

test('draft previews do not track reading or link into the live sequence', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    $draft = BlogPost::create(['title' => 'Draft', 'slug' => 'draft', 'content' => '<p>x</p>', 'status' => 'draft', 'author_id' => $admin->id]);

    $this->actingAs($admin)->get(route('blog.show', $draft))->assertOk();

    expect($admin->progressFor('blog_post'))->toBeEmpty();
});

test('the home page offers where to continue and progress per level', function () {
    flowVideo('First Video', 'beginner', 1);
    flowDoc('First Doc', 'beginner', 1);
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('home'))
        ->assertOk()
        ->assertSee('Start learning')
        ->assertSee('First Video')
        ->assertSee('Next to study')
        ->assertSee('First Doc')
        ->assertSee('Your progress');
});

test('the profile shows the plan and learning stats', function () {
    $video = flowVideo('Watched Video', 'beginner', 1);
    $student = subscribedStudent(1);
    $this->actingAs($student)->post(route('progress.update'), ['type' => 'video', 'id' => $video->id, 'completed' => 1]);

    $this->actingAs($student)->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('L1 plan')
        ->assertSee('Videos completed')
        ->assertSee('1/1');
});

test('the profile accepts a contact number and a photo, and the photo can be removed', function () {
    Storage::fake('public');
    $student = User::factory()->create();

    $this->actingAs($student)->patch(route('profile.update'), [
        'name' => 'New Name', 'email' => $student->email, 'contact' => '+258 84 123 4567',
        'photo' => UploadedFile::fake()->image('me.jpg'),
    ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

    $student->refresh();
    expect($student->contact)->toBe('+258 84 123 4567')->and($student->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($student->photo);
    $old = $student->photo;

    $this->actingAs($student)->patch(route('profile.update'), ['name' => 'New Name', 'email' => $student->email, 'remove_photo' => '1']);
    expect($student->fresh()->photo)->toBeNull();
    Storage::disk('public')->assertMissing($old);
});

test('the profile rejects a malformed phone number and a non-image photo', function () {
    $student = User::factory()->create();

    $this->actingAs($student)->patch(route('profile.update'), ['name' => 'A', 'email' => $student->email, 'contact' => 'call me maybe'])
        ->assertSessionHasErrors('contact');
    $this->actingAs($student)->patch(route('profile.update'), ['name' => 'A', 'email' => $student->email, 'photo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])
        ->assertSessionHasErrors('photo');
});

test('admin forms accept a lesson order and default it to the end', function () {
    $admin = User::factory()->create();
    $admin->forceFill(['role' => 'admin'])->save();
    flowVideo('Existing', 'beginner', 7);

    $this->actingAs($admin)->post(route('admin.videos.store'), [
        'title' => 'Appended', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'course_level' => 'beginner', 'required_access_level' => 0,
    ])->assertSessionHasNoErrors();
    expect(Video::where('title', 'Appended')->first()->sort_order)->toBe(8);

    $this->actingAs($admin)->post(route('admin.videos.store'), [
        'title' => 'Pinned', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'course_level' => 'beginner', 'required_access_level' => 0, 'sort_order' => 1,
    ]);
    expect(Video::where('title', 'Pinned')->first()->sort_order)->toBe(1);
});
