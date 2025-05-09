<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            // 'name'    => 'required|string|max:20|unique:roles,name,'.$this->id.',id',
            'name' => Rule::unique('roles', 'name')->ignore((int) $this->id, 'id'),

            'permissions' => 'nullable|array|min:1',
            'permissions.*' => 'nullable|exists:permissions,id',
        ];
        if (! $this->id) {
            array_shift($validations);
        }

        return $validations;
    }
}
