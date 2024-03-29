<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
       * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
      */
    public function rules(): array
    {
        return [
            'email'      => 'required|exists:users,email',
            'password'   => 'required|string|min:6',
            'device'     => 'required|string|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone.exists' => __('messages.phone.exists'),
            'password.required' => __('messages.password.required'),
            'password.size'     => __('messages.password.size'),
        ];
    }
}
