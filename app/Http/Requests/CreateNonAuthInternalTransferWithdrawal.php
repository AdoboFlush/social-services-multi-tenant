<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNonAuthInternalTransferWithdrawal extends FormRequest
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
            'receiver_individual_account' => 'required',
            'merchant_account' => 'required',
            'debit_currency' => 'required',
            'transaction_id' => 'required',
            'amount' => 'required|numeric',
            'signature' => 'required|unique:internal_transfers,signature',
            'currency' => 'required'
        ];
    }
}
