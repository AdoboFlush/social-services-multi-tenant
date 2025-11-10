<?php
namespace App\Http\Requests\CsvRequests;

use Validator;

class BusinessAccountWithdrawalRequests
{
    const ACCOUNT_NUMBER = 0;
    const BANK_NAME = 1;
    const BANK_CODE = 2;
    const BANK_BRANCH_NAME = 3;
    const BANK_BRANCH_CODE = 4;
    const ACCOUNT_TYPE = 5;
    const BANK_ACCOUNT_NUMBER = 6;
    const BENEFICIARY_ACCOUNT_NAME = 7;
    const AMOUNT = 8;
    const APPLICATION_DATE = 9;

    public function rules(){
        return array(
            'number' => 'required',
            'account_number' => 'required|exists:users,account_number',
            'beneficiary_bank' => 'required',
            'beneficiary_bank_code' => 'required|digits_between:4,4',
            'beneficiary_bank_branch_name' => 'required',
            'beneficiary_bank_branch_code' => 'required|digits_between:3,3',
            'account_type' => 'required',
            'bank_account_number' => 'required|digits_between:7,7',
            'account_name' => 'required',
            'currency' => 'required',
            'amount' => 'required|numeric',
            'application_date' => 'required|date',
        );
    }

    public function messages(){
        return array(
            'beneficiary_bank_code.digits_between' => _lang('Bank code must be 4 digits long'),
            'beneficiary_bank_branch_code.digits_between' => _lang('Branch code must be 3 digits long'),
            'bank_account_number.digits_between' => _lang('Account number must be 7 digits long'),
        );
    }
    public function validate($record)
    {
        $rules = $this->rules();
        $messages = $this->messages();
        $validator = Validator::make($record, $rules,$messages);
        return $validator->fails() ? $validator->errors()->all() : [];
    }
}
