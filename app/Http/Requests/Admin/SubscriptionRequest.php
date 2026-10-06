<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'payment_channel' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string'],
        ];
    }
}
