<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('plans', 'code')->ignore($this->route('plan'))],
            'fee' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'access_level' => ['required', 'integer', 'min:0', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'features' => ['nullable', 'string', 'max:2000'],
            'is_popular' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Validated data ready for the model: checkboxes as booleans, and the one-feature-per-line
     * textarea as a clean array.
     */
    public function planData(): array
    {
        $data = $this->validated();

        $data['features'] = collect(preg_split('/\R/', (string) ($data['features'] ?? '')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        $data['is_popular'] = $this->boolean('is_popular');
        $data['is_active'] = $this->boolean('is_active');

        return $data;
    }
}
