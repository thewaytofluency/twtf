<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::where('role', UserRole::Student)
                ->with('currentSubscription.plan')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $newStatus = $user->isActive() ? UserStatus::Inactive : UserStatus::Active;

        // 'status' is deliberately not in User::$fillable (blocks privilege escalation via
        // any user-facing form), so a normal mass-assigning update() would silently no-op.
        $user->forceFill(['status' => $newStatus])->save();

        return redirect()->route('admin.users.index')->with('status', "User marked {$newStatus->value}.");
    }
}
