<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileAvatarUpdateRequest extends FormRequest
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
            'id' => 'required|exists:users,id',
            'avatar' => 'required|image|max:2048',
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
                'description' => 'User ID whose avatar to update',
                'example' => 1,
            ],
            'avatar' => [
                'description' => 'Avatar image file (max 2MB, must be an image)',
                'example' => null,
            ],
        ];
    }
}
