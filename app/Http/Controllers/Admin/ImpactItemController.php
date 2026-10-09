<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesLandingImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImpactItemRequest;
use App\Models\ImpactItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImpactItemController extends Controller
{
    use HandlesLandingImage;

    public function index(): View
    {
        return view('admin.impact.index', [
            'items' => ImpactItem::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.impact.create', [
            'item' => new ImpactItem(['is_active' => true, 'sort_order' => ImpactItem::nextSortOrder()]),
        ]);
    }

    public function store(ImpactItemRequest $request): RedirectResponse
    {
        ImpactItem::create([
            ...$this->attributes($request),
            ...$this->imageData($request),
        ]);

        return redirect()->route('admin.impact.index')->with('status', 'Impact item created.');
    }

    public function edit(ImpactItem $impact): View
    {
        return view('admin.impact.edit', ['item' => $impact]);
    }

    public function update(ImpactItemRequest $request, ImpactItem $impact): RedirectResponse
    {
        $impact->update([
            ...$this->attributes($request, $impact),
            ...$this->imageData($request, $impact),
        ]);

        return redirect()->route('admin.impact.index')->with('status', 'Impact item updated.');
    }

    public function destroy(ImpactItem $impact): RedirectResponse
    {
        $this->deleteImage($impact);
        $impact->delete();

        return redirect()->route('admin.impact.index')->with('status', 'Impact item deleted.');
    }

    public function move(Request $request, ImpactItem $impact): RedirectResponse
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);

        $impact->move($request->string('direction')->toString());

        return redirect()->route('admin.impact.index');
    }

    private function attributes(ImpactItemRequest $request, ?ImpactItem $existing = null): array
    {
        $data = $request->safe()->only(['title', 'description', 'badge_type', 'stat', 'emoji']);
        $data['stat'] = filled($data['stat'] ?? null) ? $data['stat'] : null;
        $data['emoji'] = filled($data['emoji'] ?? null) ? $data['emoji'] : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $request->filled('sort_order')
            ? $request->integer('sort_order')
            : ($existing?->sort_order ?? ImpactItem::nextSortOrder());

        return $data;
    }
}
