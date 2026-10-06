<?php

use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DocController as AdminDocController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\SocialMediaLinkController as AdminSocialMediaLinkController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\VideoController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;

// Public pages — ported from src/router/index.tsx. Courses/Impact/Pricing used to be separate
// routes/views; they're now sections (#courses, #impact, #pricing) on the single fullpage
// scroll-snap landing page, so the old URLs just redirect to their anchor.
Route::get('/', function () {
    return view('welcome', [
        // Paid, active plans only � same set the in-app plan picker offers (the free tier
        // isn't something to "choose" on the pricing section).
        'plans' => Plan::active()->where('access_level', '>', 0)->orderBy('access_level')->orderBy('fee')->get(),
    ]);
});

Route::redirect('/courses', '/#courses');
Route::redirect('/impact', '/#impact');
Route::redirect('/pricing', '/#pricing');

// Protected pages — the source app's PrivateRoute only checked "is there a logged-in user"
// (its requiredRole prop was an unimplemented console.log stub), which is exactly what
// Laravel's built-in 'auth' middleware does. No role-based access control exists here.
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Phase 3 — real, access-gated content replacing the `<div>...Page</div>` placeholders
    // that stood in for these since the initial port. isAccessibleBy() is still checked
    // server-side inside VideoController@show and DocController@download themselves, not just
    // relied on via what these index pages choose to link to.
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');

    Route::get('/documents', [DocController::class, 'index'])->name('documents.index');
    Route::get('/documents/{doc}/download', [DocController::class, 'download'])->name('documents.download');
    Route::get('/studyguide', [DocController::class, 'studyGuide'])->name('documents.study-guide');

    Route::get('/blog', [BlogPostController::class, 'index'])->name('blog.index');
    Route::get('/blog/{blogPost}', [BlogPostController::class, 'show'])->name('blog.show');
    Route::post('/blog/{blogPost}/comments', [CommentController::class, 'store'])->name('blog.comments.store');

    // Phase 5 — generic polymorphic like toggle, shared by BlogPost/Video/Comment (all three
    // already use the Likeable trait + morph map from Phase 1). One route instead of three.
    Route::post('/likes/toggle', [LikeController::class, 'toggle'])->name('likes.toggle');

    // Phase 4 — student-facing subscription request flow. Requests are created 'pending'
    // and land in the admin's existing subscriptions review queue (Phase 2); approving one
    // reuses Subscription::approve(), the same code path the admin's own manual-entry flow
    // already uses.
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('/subscription/plans/{plan}/request', [SubscriptionController::class, 'request'])->name('subscription.request');
    Route::post('/subscription/plans/{plan}/request', [SubscriptionController::class, 'store'])->name('subscription.store');

    // NOTE: the source app also has a bare `<div>Profile Page</div>` placeholder at /profile.
    // That route is intentionally NOT recreated here — Breeze's own /profile (account
    // name/email/password management, ProfileController below) already owns this path as
    // part of the native auth scaffold decided on for this port. Overwriting it with a stub
    // would remove working account-management functionality that Breeze provides out of the
    // box. See ProfileController routes below.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin panel (Phase 2). `EnsureUserIsActive` doesn't need adding here — it's already
// globally appended to the 'web' middleware group in bootstrap/app.php, inherited here too.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('videos', AdminVideoController::class)->except('show');
    Route::resource('docs', AdminDocController::class)->except('show');
    Route::post('blog-posts/images', [AdminBlogPostController::class, 'uploadImage'])->name('blog-posts.images');
    Route::resource('blog-posts', AdminBlogPostController::class)->except('show');
    Route::resource('plans', AdminPlanController::class)->except('show');

    // Edit-only: platform is a closed 5-case enum, unique, seeded once — nothing to create/destroy.
    Route::resource('social-media-links', AdminSocialMediaLinkController::class)
        ->only(['index', 'edit', 'update']);

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/create', [AdminSubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('subscriptions', [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::post('subscriptions/{subscription}/approve', [AdminSubscriptionController::class, 'approve'])->name('subscriptions.approve');
    Route::post('subscriptions/{subscription}/reject', [AdminSubscriptionController::class, 'reject'])->name('subscriptions.reject');
    Route::get('subscriptions/{subscription}/proof', [AdminSubscriptionController::class, 'proof'])->name('subscriptions.proof');
});

require __DIR__.'/auth.php';
