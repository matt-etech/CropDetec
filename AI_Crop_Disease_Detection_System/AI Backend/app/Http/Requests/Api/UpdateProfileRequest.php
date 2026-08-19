<?php

namespace App\Http\Requests\Api;

use App\Support\ZimbabwePhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => ZimbabwePhone::normalize($this->input('phone'))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ZimbabwePhone::rules(sometimes: true),
            'language_preference' => ['sometimes', Rule::in(['en', 'sn'])],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a valid Zimbabwe mobile number, for example 0771234567.'];
    }
}
