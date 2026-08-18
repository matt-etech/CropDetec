<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertDiseaseRequest extends FormRequest
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
        $disease = $this->route('disease');

        return [
            'crop_id' => [$this->isMethod('post') ? 'required' : 'sometimes', 'exists:crops,id'],
            'name' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'max:255'],
            'class_label' => [
                $this->isMethod('post') ? 'required' : 'sometimes',
                'string',
                'max:255',
                Rule::unique('diseases', 'class_label')->ignore($disease?->id),
            ],
            'name_sn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'description_sn' => ['sometimes', 'nullable', 'string'],
            'symptoms' => ['sometimes', 'nullable', 'string'],
            'symptoms_sn' => ['sometimes', 'nullable', 'string'],
            'prevention' => ['sometimes', 'nullable', 'string'],
            'prevention_sn' => ['sometimes', 'nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
