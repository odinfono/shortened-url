<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Handles validation for URL decoding requests.
 */
class DecodeUrlRequest extends FormRequest
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
            'shortUrl' => 'required|url',
        ];
    }

    /**
     * Custom error messages for validation failures.
     */
    public function messages()
    {
        return [
            'shortUrl.required' => 'The Short URL field is required.',
            'shortUrl.url' => 'The Short URL format is invalid.',
        ];
    }

    /**
     * Prepares data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'shortUrl' => $this->shortUrl,
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
