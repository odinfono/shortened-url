<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EncodeUrlRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'url' => 'required|url',
        ];
    }
    public function messages()
    {
        return [
            'url.required' => 'The URL field is required.',
            'url.url' => 'The URL format is invalid.',
        ];
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'url' => $this->url,
        ]);
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()->all(),
        ], 422));
    }
}
