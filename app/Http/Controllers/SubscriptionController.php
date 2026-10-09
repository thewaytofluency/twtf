<?php

namespace App\Http\Controllers;

use App\Enums\SubscriptionMethod;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('subscription.index', [
            'currentSubscription' => $user->currentSubscription,
            'pendingSubscription' => $user->pendingSubscription,
            'history' => $user->subscriptions()->with('plan')->latest()->get(),
        ]);
    }

    public function plans(): View
    {
        return view('subscription.plans', [
            'plans' => Plan::active()->where('access_level', '>', 0)->orderBy('access_level')->get(),
            'currentPlanId' => Auth::user()->currentSubscription?->plan_id,
        ]);
    }

    public function request(Plan $plan): View|RedirectResponse
    {
        if ($redirect = $this->guardAgainstInvalidRequest($plan)) {
            return $redirect;
        }

        return view('subscription.request', [
            'plan' => $plan,
        ]);
    }

    public function store(SubscriptionRequest $request, Plan $plan): RedirectResponse
    {
        if ($redirect = $this->guardAgainstInvalidRequest($plan)) {
            return $redirect;
        }

        $proofPath = $request->file('proof')->store('proofs', 'local');

        Auth::user()->subscriptions()->create([
            'plan_id' => $plan->id,
            'method' => SubscriptionMethod::Manual,
            'payment_channel' => $request->validated('payment_channel'),
            'amount' => $plan->fee,
            'proof_path' => $proofPath,
        ]);

        return redirect()->route('subscription.index')
            ->with('status', 'Your request has been submitted and is pending review.');
    }

    /**
     * Shared by request() and store(): the Free plan has nothing to request, and a
     * student shouldn't be able to queue up a second request while one is already
     * pending review. Both are "you shouldn't be here" states, not security
     * boundaries - one aborts (real invalid target), the other redirects with a
     * message (a normal state the student caused themselves).
     */
    private function guardAgainstInvalidRequest(Plan $plan): ?RedirectResponse
    {
        abort_if($plan->access_level === 0, 404);

        if (Auth::user()->pendingSubscription) {
            return redirect()->route('subscription.index')
                ->with('info', 'You already have a subscription request pending review.');
        }

        return null;
    }
}
