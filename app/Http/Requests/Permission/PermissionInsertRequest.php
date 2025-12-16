<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionInsertRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:permissions,name',
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
            'name' => [
                'description' => 'Permission name (must be unique)',
                'example' => 'create_users',
            ],
        ];
    }
}
