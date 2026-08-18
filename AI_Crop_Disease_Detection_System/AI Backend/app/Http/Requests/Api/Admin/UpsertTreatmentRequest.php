<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpsertTreatmentRequest extends FormRequest
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
            'disease_id' => [$this->isMethod('post') ? 'required' : 'sometimes', 'exists:diseases,id'],
            'title' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string', 'max:255'],
            'title_sn' => ['sometimes', 'nullable', 'string', 'max:255'],
            'instructions' => [$this->isMethod('post') ? 'required' : 'sometimes', 'string'],
            'instructions_sn' => ['sometimes', 'nullable', 'string'],
            'type' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
