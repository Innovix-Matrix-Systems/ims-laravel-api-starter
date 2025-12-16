<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserInsertUpdateRequest extends FormRequest
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
        $validations = [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255' . ($this->id ? '|unique:users,email,' . $this->id . ',id,deleted_at,NULL' : '|unique:users,email,NULL,id,deleted_at,NULL'),
            'password' => ($this->id == null) ? 'required|string|min:6|confirmed' : '',
            'phone' => 'nullable|numeric' . ($this->id ? '|unique:users,phone,' . $this->id . ',id,deleted_at,NULL' : '|unique:users,phone,NULL,id,deleted_at,NULL'),
            'roles' => ($this->id == null) ? 'required|array|min:1' : '',
            'roles.*' => ($this->id == null) ? 'required|exists:roles,id' : '',
        ];

        return array_filter($validations);
    }

    /**
     * Get the body parameters for API documentation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'first_name' => [
                'description' => 'User first name',
                'example' => 'John',
            ],
            'last_name' => [
                'description' => 'User last name',
                'example' => 'Doe',
            ],
            'name' => [
                'description' => 'User full name',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'User email address',
                'example' => 'john.doe@example.com',
            ],
            'password' => [
                'description' => 'User password (required for new users, minimum 6 characters)',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Password confirmation (required when password is provided)',
                'example' => 'password123',
            ],
            'phone' => [
                'description' => 'User phone number',
                'example' => '1234567890',
            ],
            'is_active' => [
                'description' => 'Whether the user is active',
                'example' => true,
            ],
            'roles' => [
                'description' => 'Array of role IDs to assign to the user (required for new users)',
                'example' => [2, 3],
            ],
            'roles.*' => [
                'description' => 'Individual role ID',
                'example' => 2,
            ],
        ];
    }
}
