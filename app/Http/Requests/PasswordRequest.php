<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PasswordRequest extends FormRequest
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
     * Override failed validation response
     *
     * @param Validation $validator
     *
     * @return json
     */
    protected function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(
                response(
                    [
                        'result'=> 'error',
                        'message'=> $validator->errors()->all(),
                        'load_change_password_modal' => true
                    ]
                )
            );
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'oldpassword' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'min'=> _lang('The password must be at least 6 characters.'),
            'confirmed'=> _lang('The password confirmation does not match.'),
        ];
    }
}