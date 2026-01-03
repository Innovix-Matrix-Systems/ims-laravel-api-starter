<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AdminAssignUserRoleRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:users,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|exists:roles,id',
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
                'description' => 'ID of the user to assign roles to',
                'example' => 1,
            ],
            'roles' => [
                'description' => 'Array of role IDs to assign to the user',
                'example' => [1, 2],
            ],
            'roles.*' => [
                'description' => 'Individual role ID',
                'example' => 1,
            ],
        ];
    }
}
