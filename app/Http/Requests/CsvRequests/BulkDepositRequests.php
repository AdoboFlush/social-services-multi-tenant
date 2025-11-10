<?php
namespace App\Http\Requests\CsvRequests;

use Validator;

class BulkDepositRequests
{
    const DATE = 0;
    const ACCOUNT_NUMBER = 1;
    const CURRENCY = 2;
    const AMOUNT = 3;
    const BANK_NAME = 4;
    const NOTE = 5;
    const ADMIN_NOTE = 6;

    public function rules(){
        return array(
            'account_number' => 'exists:users,account_number',
            'amount' => 'required|numeric',
            'bank_name' => 'required',
        );
    }

    public function messages(){
        return array(
            'account_number.exists' => _lang('Account number does not exist.'),
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
