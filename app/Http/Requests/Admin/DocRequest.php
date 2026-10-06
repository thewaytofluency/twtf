<?php

namespace App\Http\Requests\Admin;

use App\Enums\CourseLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class DocRequest extends FormRequest
{
    public function rules(): array
    {
        // A doc is being created when no {doc} route-model-binding is present.
        $isCreating = $this->route('doc') === null;

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => [
                $isCreating ? 'required' : 'nullable',
                'file',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,png',
                'max:20480',
            ],
            'course_level' => ['nullable', new Enum(CourseLevel::class)],
            'required_access_level' => ['required', 'integer', 'min:0', 'max:255'],
        ];
    }
}
