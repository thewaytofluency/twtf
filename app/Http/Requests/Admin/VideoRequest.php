<?php

namespace App\Http\Requests\Admin;

use App\Enums\CourseLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class VideoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'youtube_url' => ['required', 'url', 'max:2048', 'regex:/(youtube\.com|youtu\.be)/i'],
            'course_level' => ['required', new Enum(CourseLevel::class)],
            'required_access_level' => ['required', 'integer', 'min:0', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
