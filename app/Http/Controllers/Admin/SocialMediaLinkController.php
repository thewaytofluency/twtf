<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SocialMediaLinkRequest;
use App\Models\SocialMediaLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SocialMediaLinkController extends Controller
{
    public function index(): View
    {
        return view('admin.social-media-links.index', [
            'socialMediaLinks' => SocialMediaLink::orderBy('platform')->get(),
        ]);
    }

    public function edit(SocialMediaLink $socialMediaLink): View
    {
        return view('admin.social-media-links.edit', [
            'socialMediaLink' => $socialMediaLink,
        ]);
    }

    public function update(SocialMediaLinkRequest $request, SocialMediaLink $socialMediaLink): RedirectResponse
    {
        $socialMediaLink->update([
            'url' => $request->validated('url'),
            'is_visible' => $request->boolean('is_visible'),
        ]);

        return redirect()->route('admin.social-media-links.index')->with('status', 'Social link updated.');
    }
}
