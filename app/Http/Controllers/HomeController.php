<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $accessLevel = $user->currentAccessLevel();

        return view('home', [
            'videoCount' => Video::where('required_access_level', '<=', $accessLevel)->count(),
            'docCount' => Doc::where('required_access_level', '<=', $accessLevel)->count(),
            'planName' => $user->currentSubscription?->plan?->name ?? 'Free',
            'planExpires' => $user->currentSubscription?->ends_at?->format('M j, Y'),
        ]);
    }
}
