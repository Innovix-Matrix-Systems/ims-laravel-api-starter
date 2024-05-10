<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserInsertUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'id'            => ($this->id !== null) ? 'required|exists:users,id' : '',
            'first_name'    => 'nullable|string|max:255',
            'last_name'     => 'nullable|string|max:255',
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255'.($this->id ? '|unique:users,email,'.$this->id.',id' : '|unique:users,email'),
            'password'      => ($this->id == null) ? 'required|string|min:6|confirmed' : '',
            'phone'         => 'nullable|numeric'.($this->id ? '|unique:users,phone,'.$this->id.',id' : '|unique:users,phone'),
            'designation'   => 'nullable|string|max:255',
            'address'       => 'nullable|string|max:255',
            'roles'         => ($this->id == null) ? 'required|array|min:1' : '',
            'roles.*'       => ($this->id == null) ? 'required|exists:roles,id' : '',
        ];

        return array_filter($validations);
    }
}
