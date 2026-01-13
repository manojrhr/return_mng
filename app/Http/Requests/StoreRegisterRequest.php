<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules;

class StoreRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_name' => 'required|string',
            'store_code' => 'required|string|unique:stores,store_code',
            'point_of_contact' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required|min:6'
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        if ($this->wantsJson()) {
            $errors = implode(', ', $validator->messages()->all()); // Concatenates error messages with a comma and space

            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => $errors,
            ], 422));
        }
        // Default behavior for non-JSON requests
        parent::failedValidation($validator);
    }
}
