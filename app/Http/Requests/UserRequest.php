<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email:filter,strict,dns|unique:users',
            'phone' => 'unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required',
            'country_of_residence' => 'required',
            'account_type' => 'nullable|max:15',
            'password' => 'required|max:20|min:6|confirmed',
            'status' => 'required',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'zip' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|max:5120',
            'language' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => _lang('Email already exists.'),
            'email.email' => _lang('The email address must be valid.'),
        ];
    }
}
