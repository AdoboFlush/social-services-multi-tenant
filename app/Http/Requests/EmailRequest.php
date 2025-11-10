<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmailRequest extends FormRequest
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
                        'load_change_email_modal' => true
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
            'email' => 'required|string|email|max:191|unique:users',
            'email_confirm' => 'required|email|same:email',
        ];
    }

    /**
     * Custom validation message
     *
     * @return array
     */
    public function messages()
    {
        return [
            'same'=> _lang('New Email is not the same.'),
            'unique'=> _lang('The email has already been taken.'),
        ];
    }
}