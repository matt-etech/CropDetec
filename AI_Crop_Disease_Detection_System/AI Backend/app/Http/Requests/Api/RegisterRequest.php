<?php

namespace App\Http\Requests\Api;

use App\Support\ZimbabwePhone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['phone' => ZimbabwePhone::normalize($this->input('phone'))]);
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ZimbabwePhone::rules(),
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'language_preference' => ['nullable', Rule::in(['en', 'sn'])],
        ];
    }

    public function messages(): array
    {
        return ['phone.regex' => 'Enter a valid Zimbabwe mobile number, for example 0771234567.'];
    }
}
