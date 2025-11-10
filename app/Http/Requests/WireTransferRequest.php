<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class WireTransferRequest extends FormRequest
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
            'account_id' => 'required',
            'currency' => 'required',
            'swift' => 'required',
            'customer_iban' => 'required',
            'amount' => 'required|numeric',
            'customer_name' => 'required',
            'customer_country' => 'required',
            'customer_address' => 'required',
            'bank_name' => 'required',
            'bank_code' => 'required',
            'bank_branch_name' => 'required',
            'bank_branch_code' => 'required',
            'bank_address' => 'required',
            'bank_country' => 'required'
        ];
    }
}
