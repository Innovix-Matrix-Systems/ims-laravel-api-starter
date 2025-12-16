<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserPasswordUpdateRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
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
            'user_id' => [
                'description' => 'ID of the user whose password to update',
                'example' => 1,
            ],
            'password' => [
                'description' => 'New password (minimum 6 characters)',
                'example' => 'newpassword123',
            ],
            'password_confirmation' => [
                'description' => 'Password confirmation',
                'example' => 'newpassword123',
            ],
        ];
    }
}
