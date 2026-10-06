<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaLinkRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
            'is_visible' => ['sometimes', 'boolean'],
        ];
    }
}
