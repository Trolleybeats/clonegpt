<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled in controller via policies.
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:1'],
        ];
    }
}
