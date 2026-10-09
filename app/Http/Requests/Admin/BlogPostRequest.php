<?php

namespace App\Http\Requests\Admin;

use App\Support\PostHtml;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')->ignore($this->route('blog_post'))],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                // An "empty" editor still submits markup like <p></p> - require real text or an image.
                if (PostHtml::toText($value) === '' && ! str_contains((string) $value, '<img')) {
                    $fail('The post content cannot be empty.');
                }
            }],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'remove_cover' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
