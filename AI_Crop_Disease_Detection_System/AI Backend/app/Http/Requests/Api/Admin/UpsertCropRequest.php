<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertCropRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $crop = $this->route('crop');

        return [
            'name' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
                Rule::unique('crops', 'name')->ignore($crop?->id),
            ],
            'name_sn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'scientific_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'description_sn' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
