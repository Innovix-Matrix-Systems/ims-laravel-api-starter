<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
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
            'email' => 'required|exists:users,email',
            'password' => 'required|string|min:6',
            'device' => 'required|string|max:100',
        ];
    }

    /**
     * Get the body parameters for API documentation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'email' => [
                'description' => 'User email address',
                'example' => 'superadmin@ims.com',
            ],
            'password' => [
                'description' => 'User password (minimum 6 characters)',
                'example' => '123456',
            ],
            'device' => [
                'description' => 'Device identifier for login tracking',
                'example' => 'mobile_app',
            ],
        ];
    }
}
