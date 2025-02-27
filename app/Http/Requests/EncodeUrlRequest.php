<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Handles validation for URL encoding requests.
 */
class EncodeUrlRequest extends FormRequest
{
    /**
     * Authorizes all users to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Defines validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'url' => 'required|url',
        ];
    }

    /**
     * Custom error messages for validation failures.
     */
    public function messages()
    {
        return [
            'url.required' => 'The URL field is required.',
            'url.url' => 'The URL format is invalid.',
        ];
    }

    /**
     * Prepares data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'url' => $this->url,
        ]);
    }

    /**
     * Handles failed validation by returning a JSON response.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()->all(),
        ], 422));
    }
}
