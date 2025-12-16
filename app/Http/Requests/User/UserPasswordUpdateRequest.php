<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserPasswordUpdateRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
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
            'current_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
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
            'current_password' => [
                'description' => 'Current password for verification',
                'example' => 'currentpassword123',
            ],
            'password' => [
                'description' => 'New password (minimum 6 characters)',
                'example' => 'newpassword123',
            ],
            'password_confirmation' => [
                'description' => 'New password confirmation',
                'example' => 'newpassword123',
            ],
        ];
    }
}
