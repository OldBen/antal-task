<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendSmsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => ['required', 'string', Rule::in(['Test', '2WAY'])],
            'to' => ['required', 'string', 'regex:/^[0-9]+$/i', Rule::anyOf(
                ['size:9'],
                ['size:11', 'starts_with:48']
            )],
            'message' => ['required', 'string', 'max:160'],
        ];
    }
}
