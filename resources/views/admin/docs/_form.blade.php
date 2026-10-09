@php
    $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
@endphp

<div class="space-y-4 max-w-xl">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input id="title" name="title" type="text" required class="{{ $inputClass }}" value="{{ old('title', $doc->title) }}">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea id="description" name="description" rows="3" class="{{ $inputClass }}">{{ old('description', $doc->description) }}</textarea>
    </div>

    <div>
        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File</label>
        <input id="file" name="file" type="file" @if (! $doc->exists) required @endif class="{{ $inputClass }}">
        @if ($doc->exists)
            <p class="mt-1 text-sm text-gray-500">
                Current file: {{ $doc->original_filename }} ({{ number_format($doc->file_size / 1024, 1) }} KB). Leave blank to keep it.
            </p>
        @endif
        <p class="mt-1 text-sm text-gray-500">PDF, Word, PowerPoint, Excel, ZIP, JPG or PNG - up to 20MB.</p>
    </div>

    <div>
        <label for="course_level" class="block text-sm font-medium text-gray-700 mb-1">Course Level (optional)</label>
        <select id="course_level" name="course_level" class="{{ $inputClass }}">
            <option value="">Not level-specific</option>
            @foreach (\App\Enums\CourseLevel::cases() as $level)
                <option value="{{ $level->value }}" @selected(old('course_level', $doc->course_level?->value) === $level->value)>
                    {{ $level->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="required_access_level" class="block text-sm font-medium text-gray-700 mb-1">Required Plan</label>
        <select id="required_access_level" name="required_access_level" required class="{{ $inputClass }}">
            @foreach ($plans as $plan)
                <option value="{{ $plan->access_level }}" @selected((string) old('required_access_level', $doc->required_access_level) === (string) $plan->access_level)>
                    {{ $plan->name }} (level {{ $plan->access_level }})
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-sm text-gray-500">Students on this plan or higher can download this document.</p>
    </div>

    <div>
        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Lesson order</label>
        <input id="sort_order" name="sort_order" type="number" min="0" class="{{ $inputClass }}" value="{{ old('sort_order', $doc->exists ? $doc->sort_order : '') }}" placeholder="Leave empty to add at the end">
        <p class="mt-1 text-sm text-gray-500">Position within its level. Students move through lessons in this order (previous / next, up next).</p>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
        {{ $doc->exists ? 'Save Changes' : 'Upload Document' }}
    </button>
    <a href="{{ route('admin.docs.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>
