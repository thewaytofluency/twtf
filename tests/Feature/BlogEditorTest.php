<?php

use App\Enums\UserRole;
use App\Models\BlogPost;
use App\Models\User;
use App\Support\PostHtml;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function blogAdmin(): User
{
    $admin = User::factory()->create();
    $admin->forceFill(['role' => UserRole::Admin])->save();

    return $admin;
}

function postPayload(array $overrides = []): array
{
    return [
        'title' => 'Hello World',
        'content' => '<p>Some <strong>bold</strong> text.</p>',
        'status' => 'published',
        ...$overrides,
    ];
}

test('sanitizer strips scripts, handlers, inline styles and javascript urls but keeps formatting', function () {
    $dirty = '<p onclick="steal()" style="position:fixed">Hi <strong>there</strong></p>'
        .'<script>alert(1)</script>'
        .'<a href="javascript:alert(1)">bad</a> <a href="https://example.com">good</a>'
        .'<img src="/storage/blog/images/a.jpg" alt="a" onerror="x()">'
        .'<h2 data-align="center">Title</h2>';

    $clean = PostHtml::clean($dirty);

    expect($clean)
        ->not->toContain('<script')
        ->not->toContain('onclick')
        ->not->toContain('onerror')
        ->not->toContain('style=')
        ->not->toContain('javascript:')
        ->toContain('<strong>there</strong>')
        ->toContain('href="https://example.com"')
        ->toContain('rel="noopener noreferrer nofollow"')
        ->toContain('src="/storage/blog/images/a.jpg"')
        ->toContain('data-align="center"');
});

test('legacy plain text is converted to escaped paragraphs', function () {
    $html = PostHtml::clean("First <b>para</b>\nsame para\n\nSecond para");

    expect($html)->toBe("<p>First &lt;b&gt;para&lt;/b&gt;<br />\nsame para</p>\n<p>Second para</p>");
});

test('content is sanitized when stored through the model', function () {
    $post = BlogPost::create(['title' => 'X', 'slug' => 'x', 'content' => '<p>ok</p><script>bad()</script>', 'author_id' => blogAdmin()->id]);

    expect($post->fresh()->content)->toBe('<p>ok</p>');
});

test('students cannot use the post editor or upload images', function () {
    $student = User::factory()->create();

    $this->actingAs($student)->get(route('admin.blog-posts.create'))->assertForbidden();
    $this->actingAs($student)->post(route('admin.blog-posts.images'), ['image' => UploadedFile::fake()->image('a.jpg')])->assertForbidden();
});

test('an admin can open the editor pages', function () {
    $admin = blogAdmin();
    $post = BlogPost::create(['title' => 'Edit me', 'slug' => 'edit-me', 'content' => '<p>x</p>', 'author_id' => $admin->id]);

    $this->actingAs($admin)->get(route('admin.blog-posts.create'))->assertOk()->assertSee('Add title');
    $this->actingAs($admin)->get(route('admin.blog-posts.edit', $post))->assertOk()->assertSee('Edit me');
    $this->actingAs($admin)->get(route('admin.blog-posts.index'))->assertOk();
});

test('creating a post stores sanitized html, a generated slug and publishes it', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload([
        'content' => '<p>Hello</p><script>x()</script>',
        'excerpt' => 'A summary',
    ]))->assertRedirect();

    $post = BlogPost::where('slug', 'hello-world')->firstOrFail();
    expect($post->content)->toBe('<p>Hello</p>');
    expect($post->excerpt)->toBe('A summary');
    expect($post->isPublished())->toBeTrue();
    expect($post->author_id)->toBe($admin->id);
});

test('slugs are unique and an explicit slug is respected', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload());
    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload());
    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['slug' => 'custom-url']));

    expect(BlogPost::pluck('slug')->all())->toEqualCanonicalizing(['hello-world', 'hello-world-2', 'custom-url']);
});

test('editing keeps the slug unless it is changed on purpose', function () {
    $admin = blogAdmin();
    $post = BlogPost::create(['title' => 'Original', 'slug' => 'original', 'content' => '<p>x</p>', 'author_id' => $admin->id]);

    $this->actingAs($admin)->put(route('admin.blog-posts.update', $post), postPayload(['title' => 'Renamed', 'slug' => 'original']));
    expect($post->fresh()->slug)->toBe('original');

    $this->actingAs($admin)->put(route('admin.blog-posts.update', $post), postPayload(['title' => 'Renamed', 'slug' => 'brand-new']));
    expect($post->fresh()->slug)->toBe('brand-new');
});

test('empty editor content is rejected but an image-only post is fine', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['content' => '<p></p>']))
        ->assertSessionHasErrors('content');

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['content' => '<p><img src="/storage/blog/images/a.jpg" alt=""></p>']))
        ->assertSessionDoesntHaveErrors();
});

test('drafts and scheduled posts are hidden from students but previewable by admins', function () {
    $admin = blogAdmin();
    $student = User::factory()->create();

    $draft = BlogPost::create(['title' => 'Secret Draft', 'slug' => 'secret-draft', 'content' => '<p>x</p>', 'status' => 'draft', 'author_id' => $admin->id]);
    $scheduled = BlogPost::create(['title' => 'Future Post', 'slug' => 'future-post', 'content' => '<p>x</p>', 'status' => 'published', 'published_at' => now()->addWeek(), 'author_id' => $admin->id]);
    $live = BlogPost::create(['title' => 'Live Post', 'slug' => 'live-post', 'content' => '<p>x</p>', 'author_id' => $admin->id]);

    $this->actingAs($student)->get(route('blog.index'))
        ->assertSee('Live Post')->assertDontSee('Secret Draft')->assertDontSee('Future Post');
    $this->actingAs($student)->get(route('blog.show', $draft))->assertNotFound();
    $this->actingAs($student)->get(route('blog.show', $scheduled))->assertNotFound();
    $this->actingAs($student)->post(route('blog.comments.store', $draft), ['content' => 'hi'])->assertNotFound();
    $this->actingAs($student)->get(route('blog.show', $live))->assertOk();

    $this->actingAs($admin)->get(route('blog.show', $draft))->assertOk()->assertSee('Preview');
});

test('publishing without a date goes live now, and a future date schedules the post', function () {
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['title' => 'Now']));
    expect(BlogPost::where('slug', 'now')->first()->isPublished())->toBeTrue();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['title' => 'Later', 'published_at' => now()->addDays(3)->format('Y-m-d\TH:i')]));
    expect(BlogPost::where('slug', 'later')->first()->isScheduled())->toBeTrue();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['title' => 'Wip', 'status' => 'draft']));
    expect(BlogPost::where('slug', 'wip')->first()->status)->toBe('draft');
});

test('inline images upload to the public disk and return their url', function () {
    Storage::fake('public');

    $response = $this->actingAs(blogAdmin())
        ->postJson(route('admin.blog-posts.images'), ['image' => UploadedFile::fake()->image('diagram.png')])
        ->assertOk();

    $url = $response->json('url');
    expect($url)->toStartWith('/storage/blog/images/');
    Storage::disk('public')->assertExists(str_replace('/storage/', '', $url));
});

test('non-images are rejected by the image upload', function () {
    Storage::fake('public');

    $this->actingAs(blogAdmin())
        ->postJson(route('admin.blog-posts.images'), ['image' => UploadedFile::fake()->create('evil.svg', 5, 'image/svg+xml')])
        ->assertUnprocessable();
});

test('cover images can be uploaded, replaced and removed, and are deleted with the post', function () {
    Storage::fake('public');
    $admin = blogAdmin();

    $this->actingAs($admin)->post(route('admin.blog-posts.store'), postPayload(['cover' => UploadedFile::fake()->image('c1.jpg')]));
    $post = BlogPost::firstOrFail();
    $first = $post->cover_image;
    Storage::disk('public')->assertExists($first);

    $this->actingAs($admin)->put(route('admin.blog-posts.update', $post), postPayload(['cover' => UploadedFile::fake()->image('c2.jpg')]));
    Storage::disk('public')->assertMissing($first);
    $second = $post->fresh()->cover_image;
    Storage::disk('public')->assertExists($second);

    $this->actingAs($admin)->put(route('admin.blog-posts.update', $post), postPayload(['remove_cover' => '1']));
    expect($post->fresh()->cover_image)->toBeNull();
    Storage::disk('public')->assertMissing($second);
});

test('read time and excerpt come from the text, not the markup', function () {
    $post = new BlogPost(['content' => '<p>One</p><p>two</p>']);

    expect($post->excerpt)->toBe('One two');
    expect($post->readTimeMinutes)->toBe(1);
});
