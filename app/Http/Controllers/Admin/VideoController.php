<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VideoRequest;
use App\Models\Plan;
use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VideoController extends Controller
{
    public function index(): View
    {
        return view('admin.videos.index', [
            'videos' => Video::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.videos.create', [
            'video' => new Video,
            'plans' => Plan::orderBy('access_level')->get(),
        ]);
    }

    public function store(VideoRequest $request): RedirectResponse
    {
        Video::create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.videos.index')->with('status', 'Video created.');
    }

    public function edit(Video $video): View
    {
        return view('admin.videos.edit', [
            'video' => $video,
            'plans' => Plan::orderBy('access_level')->get(),
        ]);
    }

    public function update(VideoRequest $request, Video $video): RedirectResponse
    {
        $video->update($request->validated());

        return redirect()->route('admin.videos.index')->with('status', 'Video updated.');
    }

    public function destroy(Video $video): RedirectResponse
    {
        $video->delete();

        return redirect()->route('admin.videos.index')->with('status', 'Video deleted.');
    }
}
