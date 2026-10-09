<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesLandingImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    use HandlesLandingImage;

    public function index(): View
    {
        return view('admin.courses.index', [
            'courses' => Course::ordered()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.create', [
            'course' => new Course(['accent' => 'blue', 'is_active' => true, 'sort_order' => Course::nextSortOrder()]),
        ]);
    }

    public function store(CourseRequest $request): RedirectResponse
    {
        Course::create([
            ...$this->attributes($request),
            ...$this->imageData($request),
        ]);

        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.edit', ['course' => $course]);
    }

    public function update(CourseRequest $request, Course $course): RedirectResponse
    {
        $course->update([
            ...$this->attributes($request, $course),
            ...$this->imageData($request, $course),
        ]);

        return redirect()->route('admin.courses.index')->with('status', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->deleteImage($course);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }

    public function move(Request $request, Course $course): RedirectResponse
    {
        $request->validate(['direction' => ['required', 'in:up,down']]);

        $course->move($request->string('direction')->toString());

        return redirect()->route('admin.courses.index');
    }

    private function attributes(CourseRequest $request, ?Course $existing = null): array
    {
        $data = $request->safe()->only(['title', 'description', 'badge_type', 'emoji', 'accent', 'cta_url']);
        $data['emoji'] = filled($data['emoji'] ?? null) ? $data['emoji'] : null;
        $data['cta_url'] = filled($data['cta_url'] ?? null) ? $data['cta_url'] : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $request->filled('sort_order')
            ? $request->integer('sort_order')
            : ($existing?->sort_order ?? Course::nextSortOrder());

        return $data;
    }
}
