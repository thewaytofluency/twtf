<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Doc;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'pendingSubscriptions' => Subscription::where('status', SubscriptionStatus::Pending)->count(),
            'totalStudents' => User::where('role', UserRole::Student)->count(),
            'totalVideos' => Video::count(),
            'totalDocs' => Doc::count(),
            'totalBlogPosts' => BlogPost::count(),
        ]);
    }
}
