<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        return view('admin.plans.index', [
            'plans' => Plan::withCount('subscriptions')->orderBy('access_level')->orderBy('fee')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.plans.create', [
            'plan' => new Plan(['is_active' => true]),
        ]);
    }

    public function store(PlanRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->planData();
            $plan = Plan::create($data);
            $this->keepSinglePopular($plan);
        });

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    public function edit(Plan $plan): View
    {
        return view('admin.plans.edit', ['plan' => $plan]);
    }

    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        DB::transaction(function () use ($request, $plan) {
            $plan->update($request->planData());
            $this->keepSinglePopular($plan);
        });

        return redirect()->route('admin.plans.index')->with('status', 'Plan updated.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        // subscriptions.plan_id is restrictOnDelete - check up front for a friendly message
        // instead of surfacing a foreign key exception.
        if ($plan->subscriptions()->exists()) {
            return back()->withErrors(['plan' => "\"{$plan->name}\" has subscriptions and can't be deleted. Deactivate it instead to hide it from the site."]);
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', 'Plan deleted.');
    }

    /** At most one plan carries the "Most Popular" badge. */
    private function keepSinglePopular(Plan $plan): void
    {
        if ($plan->is_popular) {
            Plan::whereKeyNot($plan->getKey())->where('is_popular', true)->update(['is_popular' => false]);
        }
    }
}
