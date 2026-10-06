@php
    $inputClass = 'w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-blue-600 focus:border-blue-600';
@endphp

<div class="space-y-4 max-w-xl">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input id="title" name="title" type="text" required class="{{ $inputClass }}" value="{{ old('title', $video->title) }}">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
        <textarea id="description" name="description" rows="3" class="{{ $inputClass }}">{{ old('description', $video->description) }}</textarea>
    </div>

    <div>
        <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-1">YouTube URL</label>
        <input id="youtube_url" name="youtube_url" type="url" required placeholder="https://www.youtube.com/watch?v=..." class="{{ $inputClass }}" value="{{ old('youtube_url', $video->youtube_url) }}">
    </div>

    <div>
        <label for="course_level" class="block text-sm font-medium text-gray-700 mb-1">Course Level</label>
        <select id="course_level" name="course_level" required class="{{ $inputClass }}">
            <option value="">Select a level</option>
            @foreach (\App\Enums\CourseLevel::cases() as $level)
                <option value="{{ $level->value }}" @selected(old('course_level', $video->course_level?->value) === $level->value)>
                    {{ $level->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="required_access_level" class="block text-sm font-medium text-gray-700 mb-1">Required Plan</label>
        <select id="required_access_level" name="required_access_level" required class="{{ $inputClass }}">
            @foreach ($plans as $plan)
                <option value="{{ $plan->access_level }}" @selected((string) old('required_access_level', $video->required_access_level) === (string) $plan->access_level)>
                    {{ $plan->name }} (level {{ $plan->access_level }})
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-sm text-gray-500">Students on this plan or higher can watch this video.</p>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-full hover:bg-blue-700 transition">
        {{ $video->exists ? 'Save Changes' : 'Create Video' }}
    </button>
    <a href="{{ route('admin.videos.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>
