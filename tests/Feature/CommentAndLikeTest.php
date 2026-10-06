<?php

use App\Models\BlogPost;
use App\Models\Comment;
use App\Models\User;
use App\Models\Video;

function makeBlogPost(): BlogPost
{
    $author = User::factory()->create();

    return BlogPost::create([
        'title' => 'Test Post',
        'slug' => 'test-post-'.uniqid(),
        'content' => 'Some content.',
        'author_id' => $author->id,
    ]);
}

test('a student can post a comment, which increments the post comment_count', function () {
    $blogPost = makeBlogPost();
    $student = User::factory()->create();

    $this->actingAs($student)
        ->post(route('blog.comments.store', $blogPost), ['content' => 'Nice post!'])
        ->assertRedirect(route('blog.show', $blogPost));

    expect($blogPost->fresh()->comment_count)->toBe(1);

    $comment = Comment::where('blog_post_id', $blogPost->id)->first();
    expect($comment->content)->toBe('Nice post!');
    expect($comment->user_id)->toBe($student->id);
});

test('a student can like and unlike a blog post via the generic toggle route', function () {
    $blogPost = makeBlogPost();
    $student = User::factory()->create();

    $this->actingAs($student)->post(route('likes.toggle'), ['type' => 'blog_post', 'id' => $blogPost->id]);
    expect($blogPost->fresh()->like_count)->toBe(1);
    expect($blogPost->fresh()->isLikedBy($student))->toBeTrue();

    $this->actingAs($student)->post(route('likes.toggle'), ['type' => 'blog_post', 'id' => $blogPost->id]);
    expect($blogPost->fresh()->like_count)->toBe(0);
    expect($blogPost->fresh()->isLikedBy($student))->toBeFalse();
});

test('the same toggle route works for a video, proving it is genuinely polymorphic', function () {
    $admin = User::factory()->create();
    $video = Video::create([
        'title' => 'Test Video',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'course_level' => 'beginner',
        'required_access_level' => 0,
        'created_by' => $admin->id,
    ]);
    $student = User::factory()->create();

    $this->actingAs($student)->post(route('likes.toggle'), ['type' => 'video', 'id' => $video->id]);

    expect($video->fresh()->like_count)->toBe(1);
});

test('the same toggle route works for a comment too', function () {
    $blogPost = makeBlogPost();
    $student = User::factory()->create();
    $comment = $blogPost->comments()->create(['user_id' => $student->id, 'content' => 'A comment']);

    $this->actingAs($student)->post(route('likes.toggle'), ['type' => 'comment', 'id' => $comment->id]);

    expect($comment->fresh()->like_count)->toBe(1);
});

test('an invalid likeable type is rejected', function () {
    $student = User::factory()->create();

    $this->actingAs($student)
        ->post(route('likes.toggle'), ['type' => 'user', 'id' => $student->id])
        ->assertSessionHasErrors('type');
});
