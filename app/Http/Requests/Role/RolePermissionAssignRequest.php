<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class RolePermissionAssignRequest extends FormRequest
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
            'id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array|min:1',
            'permissions.*' => 'nullable|exists:permissions,id',
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
            'id' => [
                'description' => 'Role ID to assign permissions to',
                'example' => 1,
            ],
            'permissions' => [
                'description' => 'Array of permission IDs to assign to the role',
                'example' => [1, 2, 3],
            ],
            'permissions.*' => [
                'description' => 'Individual permission ID',
                'example' => 1,
            ],
        ];
    }
}
