<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImpactItemRequest extends FormRequest
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
            'stat' => ['nullable', 'string', 'max:40'],
            'badge_type' => ['required', Rule::in(['emoji', 'image'])],
            'emoji' => ['nullable', 'string', 'max:16'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'remove_image' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_active' => ['boolean'],
        ];
    }
}
