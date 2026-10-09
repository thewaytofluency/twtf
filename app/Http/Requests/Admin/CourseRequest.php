<?php

namespace App\Http\Requests\Admin;

use App\Models\Course;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
{
    /** Choosing "image" needs an image: a new upload, or an existing one that isn't being removed. */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($this->input('badge_type') !== 'image' || $this->hasFile('image')) {
                return;
            }

            $existing = $this->route('course') ?? $this->route('impact');

            if (! $existing?->image || $this->boolean('remove_image')) {
                $validator->errors()->add('image', 'Upload an image, or choose the emoji badge instead.');
            }
        }];
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:400'],
            'badge_type' => ['required', Rule::in(['emoji', 'image'])],
            'emoji' => ['nullable', 'string', 'max:16'],
            'accent' => ['required', Rule::in(array_keys(Course::ACCENTS))],
            'cta_url' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/|#)/i'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'remove_image' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'cta_url.regex' => 'The button link must start with http://, https://, / or #.',
        ];
    }
}
