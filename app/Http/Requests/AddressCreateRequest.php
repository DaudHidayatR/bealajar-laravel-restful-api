<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

/**@mixin \App\Models\Address */
class AddressCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'street' => ['nullable', 'string', 'max:200'],
            'state' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10']
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors"=>$validator->getMessageBag(),
        ],400));
    }
}
