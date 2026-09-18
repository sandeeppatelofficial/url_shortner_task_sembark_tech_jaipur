<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShortUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['admin', 'member'], true);
    }

    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'original_url.required' => 'Please enter a URL to shorten.',
            'original_url.url' => 'Please enter a valid URL, including http:// or https://.',
        ];
    }
}
