<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileUpdateRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->user()?->id ?? 'NULL';

        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId . ',id',
            'phone' => 'nullable|numeric|unique:users,phone,' . $userId . ',id',
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
            'phone' => [
                'description' => 'User phone number',
                'example' => '1234567890',
            ],
        ];
    }
}
