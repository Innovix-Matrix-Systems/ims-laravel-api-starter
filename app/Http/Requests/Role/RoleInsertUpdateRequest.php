<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class RoleInsertUpdateRequest extends FormRequest
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
        $validations = [
            'name' => 'required|string|max:20|unique:roles,name,' . $this->id . ',id',
            'permissions' => 'nullable|array|min:1',
            'permissions.*' => 'nullable|exists:permissions,id',
        ];
        if (! $this->id) {
            array_shift($validations);
        }

        return $validations;
    }

    /**
     * Get the body parameters for API documentation.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Role name (maximum 20 characters)',
                'example' => 'Manager',
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
