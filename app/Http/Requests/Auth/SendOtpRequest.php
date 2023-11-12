<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $validations = [
            'firstLogin' => 'required|boolean',
            'phone'      => 'required|max:11|exists:users,phone',
            'pin'        => 'required_if:firstLogin,true|string|size:6',
        ];
        if(! $this->firstLogin) {
            array_pop($validations);
        }
        return $validations;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone.exists'      => __('messages.phone.exists'),
            'phone.max'         => __('messages.phone.max'),
            'pin.required_if'   => __('messages.pin.required'),
            'pin.size'          => __('messages.pin.size'),
        ];
    }
}
