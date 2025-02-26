<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DecodeUrlRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'shortUrl' => 'required|url',
        ];
    }
    public function messages()
    {
        return [
            'shortUrl.required' => 'The Short URL field is required.',
            'shortUrl.url' => 'The Short URL format is invalid.',
        ];
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'shortUrl' => $this->shortUrl,
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
