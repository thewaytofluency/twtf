<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionMethod;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubscriptionRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', SubscriptionStatus::Pending->value);

        return view('admin.subscriptions.index', [
            'subscriptions' => Subscription::with(['user', 'plan'])
                ->when($status !== 'all', fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.subscriptions.create', [
            'students' => User::where('role', UserRole::Student)->orderBy('name')->get(),
            'plans' => Plan::active()->orderBy('access_level')->get(),
        ]);
    }

    public function store(SubscriptionRequest $request): RedirectResponse
    {
        $plan = Plan::findOrFail($request->validated('plan_id'));

        // Created as 'pending' (the DB default) then immediately approved - same code
        // path the review-queue approve button uses, just admin-initiated instead of
        // triggered by a student-submitted payment proof (see SubscriptionController,
        // the student-facing one, for that flow - its requests stay pending for review).
        $subscription = Subscription::create([
            'user_id' => $request->validated('user_id'),
            'plan_id' => $plan->id,
            'method' => SubscriptionMethod::Manual,
            'payment_channel' => $request->validated('payment_channel'),
            'amount' => $plan->fee,
        ]);

        $subscription->approve(Auth::user());

        if ($notes = $request->validated('admin_notes')) {
            $subscription->update(['admin_notes' => $notes]);
        }

        return redirect()->route('admin.subscriptions.index')->with('status', 'Subscription recorded and activated.');
    }

    public function approve(Subscription $subscription): RedirectResponse
    {
        $subscription->approve(Auth::user());

        return redirect()->route('admin.subscriptions.index')->with('status', 'Subscription approved.');
    }

    public function reject(Request $request, Subscription $subscription): RedirectResponse
    {
        $subscription->reject(Auth::user(), $request->input('admin_notes'));

        return redirect()->route('admin.subscriptions.index')->with('status', 'Subscription rejected.');
    }

    public function proof(Subscription $subscription): StreamedResponse
    {
        abort_if(! $subscription->proof_path, 404);

        return Storage::disk('local')->response($subscription->proof_path);
    }
}
