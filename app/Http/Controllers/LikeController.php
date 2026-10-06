<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(LikeRequest $request): RedirectResponse
    {
        $modelClass = Relation::getMorphedModel($request->validated('type'));
        abort_if(! $modelClass, 404);

        $model = $modelClass::findOrFail($request->validated('id'));
        $user = Auth::user();

        $existing = $model->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            // Deleting the retrieved instance (not a query-builder ->delete()) so the
            // Eloquent event pipeline actually fires — LikeObserver::deleted() is what
            // decrements like_count, and that only runs on model-level deletes.
            $existing->delete();
        } else {
            try {
                $model->likes()->create(['user_id' => $user->id]);
            } catch (UniqueConstraintViolationException) {
                // Two near-simultaneous submits (e.g. a double-click) both passed the
                // "not liked yet" check above; the unique constraint on
                // (user_id, likeable_type, likeable_id) caught the second one. The like
                // already exists either way — nothing left to do.
            }
        }

        return redirect()->back();
    }
}
