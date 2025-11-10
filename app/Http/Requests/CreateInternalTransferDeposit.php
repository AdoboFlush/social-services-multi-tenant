<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class CreateInternalTransferDeposit extends FormRequest
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
            'merchant_account' => 'required',
            'account_id' => 'required',
            'amount' => 'required|numeric',
            'currency' => 'required',
            'signature' => 'required'
        ];
    }
}
