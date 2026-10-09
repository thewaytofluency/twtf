<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Mark a lesson (video or document) complete / not complete. One endpoint for both so the
 * "Mark complete" button, the video player's auto-complete on ending, and the "Complete and
 * continue" button all go through the same access check.
 */
class ProgressController extends Controller
{
    private const TYPES = ['video' => Video::class, 'doc' => Doc::class];

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:video,doc'],
            'id' => ['required', 'integer'],
            'completed' => ['required', 'boolean'],
            'continue' => ['sometimes', 'boolean'],
        ]);

        $model = self::TYPES[$data['type']]::findOrFail($data['id']);
        $user = Auth::user();

        // Same rule as showing the lesson: a student can't record progress on content their plan locks.
        abort_unless($model->isAccessibleBy($user), 403);

        $user->setCompleted($model, (bool) $data['completed']);

        if ($request->expectsJson()) {
            return response()->json(['completed' => (bool) $data['completed']]);
        }

        // "Complete and continue": straight on to the next lesson if the plan unlocks it.
        if (($data['continue'] ?? false) && $data['completed']) {
            $next = $model->neighbours()['next'];

            if ($next && $next->isAccessibleBy($user)) {
                return redirect()->route($data['type'] === 'video' ? 'videos.show' : 'documents.show', $next);
            }
        }

        return back()->with('status', $data['completed'] ? 'Marked as complete.' : 'Marked as not complete.');
    }
}
