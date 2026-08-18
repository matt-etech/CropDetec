<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'crop_id' => ['nullable', 'exists:crops,id'],
        ];
    }
}
