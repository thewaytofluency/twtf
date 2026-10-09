<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Support\LearningSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * The student's profile: who they are, their plan, how far they've got, and the account forms.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $summary = new LearningSummary($user);
        $resume = $summary->resumeVideo();

        return view('profile.edit', [
            'user' => $user,
            'subscription' => $user->currentSubscription?->load('plan'),
            'pendingSubscription' => $user->pendingSubscription,
            'stats' => $summary->stats(),
            'levelProgress' => $summary->levelProgress(),
            'recent' => $summary->recent(6),
            'resume' => $resume,
            'resumeStarted' => $resume ? $summary->hasStarted($resume) : false,
            'nextDoc' => $summary->nextDoc(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'email', 'contact']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('photo')) {
            $this->deletePhoto($user);
            $user->photo = $request->file('photo')->store('avatars', 'public');
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($user);
            $user->photo = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function deletePhoto($user): void
    {
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
    }
}
