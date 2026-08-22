<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public pages — ported from src/router/index.tsx. Courses/Impact/Pricing used to be separate
// routes/views; they're now sections (#courses, #impact, #pricing) on the single fullpage
// scroll-snap landing page, so the old URLs just redirect to their anchor.
Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/courses', '/#courses');
Route::redirect('/impact', '/#impact');
Route::redirect('/pricing', '/#pricing');

// Protected pages — the source app's PrivateRoute only checked "is there a logged-in user"
// (its requiredRole prop was an unimplemented console.log stub), which is exactly what
// Laravel's built-in 'auth' middleware does. No role-based access control exists here.
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    // These were literally `<div>...Page</div>` placeholders behind PrivateRoute in the
    // source app — recreated as equally minimal placeholder views, not fleshed out further.
    Route::view('/videos', 'placeholders.videos');
    Route::view('/documents', 'placeholders.documents');
    Route::view('/blog', 'placeholders.blog');
    Route::view('/studyguide', 'placeholders.studyguide');

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

require __DIR__.'/auth.php';
