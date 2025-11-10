<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InternalTransferRequest extends FormRequest
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
                        'result' => 'error',
                        'error' => $validator->errors()->all()
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
            'amount' => 'required|numeric',
            'debit_account' => 'required',
            'internal_transfer_currency' => 'required|in:1,2',
            'beneficiary_currency' => 'required',
            'beneficiary_account' => 'required'
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
            'internal_transfer_currency.required' => 'Please choose Debit Currency or Beneficiary Currency.',
        ];
    }
}